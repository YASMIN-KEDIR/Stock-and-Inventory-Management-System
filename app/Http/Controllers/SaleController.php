<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('info.index', array_merge(['tab' => 'invoices'], $request->all()));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)
            ->withSum('sales as outstanding_debt', 'remaining_balance')
            ->orderBy('name', 'asc')
            ->get();
        $products = Product::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'sale_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.line_discount' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'reference_number' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'receipt_screenshot' => ['nullable', 'image', 'max:10240'],
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_screenshot') && $request->file('receipt_screenshot')->isValid()) {
            $receiptPath = $request->file('receipt_screenshot')->store('receipts', 'public');
        }

        $sale = DB::transaction(function () use ($validated, $request, $receiptPath) {
            $productIds = collect($validated['items'])->pluck('product_id')->unique()->toArray();

            // Lock products for update
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            foreach ($validated['items'] as $itemData) {
                $prod = $products->get($itemData['product_id']);
                if (!$prod) {
                    throw ValidationException::withMessages(['items' => 'Invalid product selected.']);
                }

                $lineDisc = (float) ($itemData['line_discount'] ?? 0);
                $lineSubtotal = ($itemData['quantity'] * $itemData['unit_price']) - $lineDisc;
                $subtotal += $lineSubtotal;
            }

            $orderDiscount = (float) ($validated['discount_amount'] ?? 0);
            $taxAmount = (float) ($validated['tax_amount'] ?? 0);
            $grandTotal = max(0, $subtotal - $orderDiscount + $taxAmount);

            $amountPaid = (float) ($validated['amount_paid'] ?? 0);
            $remainingBalance = max(0, $grandTotal - $amountPaid);

            if ($remainingBalance > 0 && empty($validated['customer_id'])) {
                throw ValidationException::withMessages([
                    'customer_id' => 'Credit sale (ያልተከፈለ ብድር) requires selecting or adding a Customer to track who owes this balance.',
                ]);
            }

            $paymentStatus = 'UNPAID';
            if ($amountPaid >= $grandTotal && $grandTotal > 0) {
                $paymentStatus = 'PAID';
            } elseif ($amountPaid > 0) {
                $paymentStatus = 'PARTIALLY_PAID';
            }

            // Generate simple, clean sequential invoice number (e.g. INV-0004)
            $nextSaleId = (Sale::max('id') ?? 0) + 1;
            $invoiceNumber = 'INV-' . str_pad($nextSaleId, 4, '0', STR_PAD_LEFT);

            // Create sales invoice header
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => Auth::id(),
                'invoice_number' => $invoiceNumber,
                'sale_date' => $validated['sale_date'],
                'subtotal' => $subtotal,
                'discount_amount' => $orderDiscount,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'amount_paid' => $amountPaid,
                'remaining_balance' => $remainingBalance,
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'] ?? 'CASH',
                'receipt_image' => $receiptPath,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create sale items, snapshot cost price, and atomically decrement stock
            foreach ($validated['items'] as $itemData) {
                $prod = $products->get($itemData['product_id']);
                $lineDisc = (float) ($itemData['line_discount'] ?? 0);
                $lineSubtotal = ($itemData['quantity'] * $itemData['unit_price']) - $lineDisc;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $prod->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'line_discount' => $lineDisc,
                    'subtotal' => $lineSubtotal,
                    'cost_price_at_sale' => $prod->cost_price, // Snapshot cost price for historical margin accuracy
                ]);

                // Atomic stock decrement
                $balanceBefore = $prod->current_stock;
                $balanceAfter = $balanceBefore - $itemData['quantity'];

                $prod->update(['current_stock' => $balanceAfter]);

                // Append-only stock transaction ledger record
                StockTransaction::create([
                    'product_id' => $prod->id,
                    'user_id' => Auth::id(),
                    'type' => 'SALE',
                    'quantity' => -$itemData['quantity'],
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => 'Sale',
                    'reference_id' => $sale->id,
                    'reason' => 'Stock-Out for Sales Invoice ' . $invoiceNumber,
                ]);
            }

            // Record initial customer payment if paid > 0
            if ($amountPaid > 0) {
                $nextPayId = (CustomerPayment::max('id') ?? 0) + 1;
                $paymentNumber = 'REC-' . date('Ym') . '-' . str_pad($nextPayId, 4, '0', STR_PAD_LEFT);

                CustomerPayment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $validated['customer_id'] ?? null,
                    'user_id' => Auth::id(),
                    'payment_number' => $paymentNumber,
                    'payment_date' => $validated['sale_date'],
                    'amount' => $amountPaid,
                    'payment_method' => $validated['payment_method'] ?? 'CASH',
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => 'Initial payment upon sales order completion',
                ]);
            }

            // Record system audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'SALE_CONFIRM',
                'entity_type' => 'Sale',
                'entity_id' => $sale->id,
                'new_values' => $sale->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Sale Invoice ' . $sale->invoice_number . ' generated and inventory stock decremented safely.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product', 'payments.user']);

        return view('sales.show', compact('sale'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product', 'payments.user']);

        return view('sales.print', compact('sale'));
    }
}
