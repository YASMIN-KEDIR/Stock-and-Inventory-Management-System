<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('purchase_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $purchases = $query->latest('purchase_date')->paginate(10)->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name', 'asc')->get();
        $products = Product::where('is_active', true)->orderBy('name', 'asc')->get();
        $selectedProductId = $request->query('product_id');

        return view('purchases.create', compact('suppliers', 'products', 'selectedProductId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_cost' => ['nullable', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string'],
            'reference_number' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $purchase = DB::transaction(function () use ($validated, $request) {
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }

            $tax = (float) ($validated['tax_amount'] ?? 0);
            $shipping = (float) ($validated['shipping_cost'] ?? 0);
            $discount = (float) ($validated['discount_amount'] ?? 0);
            $grandTotal = $subtotal + $tax + $shipping - $discount;

            $amountPaid = (float) ($validated['amount_paid'] ?? 0);
            $remainingBalance = max(0, $grandTotal - $amountPaid);

            $paymentStatus = 'UNPAID';
            if ($amountPaid >= $grandTotal && $grandTotal > 0) {
                $paymentStatus = 'PAID';
            } elseif ($amountPaid > 0) {
                $paymentStatus = 'PARTIALLY_PAID';
            }

            // Generate unique purchase number (PO-YYYYMM-XXXX) using max ID
            $nextPurchaseId = (Purchase::max('id') ?? 0) + 1;
            $purchaseNumber = 'PO-' . date('Ym') . '-' . str_pad($nextPurchaseId, 4, '0', STR_PAD_LEFT);

            // Create purchase header
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'user_id' => Auth::id(),
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $validated['purchase_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_cost' => $shipping,
                'discount_amount' => $discount,
                'grand_total' => $grandTotal,
                'amount_paid' => $amountPaid,
                'remaining_balance' => $remainingBalance,
                'payment_status' => $paymentStatus,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create purchase line items and atomically update stock
            foreach ($validated['items'] as $itemData) {
                $lineSubtotal = $itemData['quantity'] * $itemData['unit_cost'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'subtotal' => $lineSubtotal,
                ]);

                // Atomic stock increment with row locking
                $product = Product::where('id', $itemData['product_id'])->lockForUpdate()->first();
                $balanceBefore = $product->current_stock;
                $balanceAfter = $balanceBefore + $itemData['quantity'];

                $product->update([
                    'current_stock' => $balanceAfter,
                    'cost_price' => $itemData['unit_cost'], // Update current cost price to latest batch
                ]);

                // Append-only stock transaction ledger record
                StockTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'PURCHASE',
                    'quantity' => $itemData['quantity'],
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'reference_type' => 'Purchase',
                    'reference_id' => $purchase->id,
                    'reason' => 'Stock-In from Purchase Order ' . $purchaseNumber,
                ]);
            }

            // Record initial supplier payment if paid > 0
            if ($amountPaid > 0) {
                $nextSpayId = (SupplierPayment::max('id') ?? 0) + 1;
                $paymentNumber = 'SPAY-' . date('Ym') . '-' . str_pad($nextSpayId, 4, '0', STR_PAD_LEFT);

                SupplierPayment::create([
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $validated['supplier_id'],
                    'user_id' => Auth::id(),
                    'payment_number' => $paymentNumber,
                    'payment_date' => $validated['purchase_date'],
                    'amount' => $amountPaid,
                    'payment_method' => $validated['payment_method'] ?? 'CASH',
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => 'Initial payment upon purchase order creation',
                ]);
            }

            // Record system audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'PURCHASE_CONFIRM',
                'entity_type' => 'Purchase',
                'entity_id' => $purchase->id,
                'new_values' => $purchase->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase Order ' . $purchase->purchase_number . ' recorded and stock updated atomically.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product', 'payments.user']);

        return view('purchases.show', compact('purchase'));
    }
}
