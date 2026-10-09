<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'customer');

        if ($tab === 'supplier') {
            $payments = SupplierPayment::with(['supplier', 'purchase', 'user'])
                ->latest('payment_date')
                ->paginate(10)
                ->withQueryString();
        } else {
            $payments = CustomerPayment::with(['customer', 'sale', 'user'])
                ->latest('payment_date')
                ->paginate(10)
                ->withQueryString();
        }

        return view('payments.index', compact('payments', 'tab'));
    }

    public function createCustomerPayment(Request $request)
    {
        $saleId = $request->query('sale_id');
        $sale = $saleId ? Sale::with('customer')->findOrFail($saleId) : null;
        $unpaidSales = Sale::with('customer')->where('remaining_balance', '>', 0)->orderBy('sale_date', 'asc')->get();

        return view('payments.create_customer', compact('sale', 'unpaidSales'));
    }

    public function storeCustomerPayment(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string'],
            'reference_number' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = DB::transaction(function () use ($validated, $request) {
            $sale = Sale::where('id', $validated['sale_id'])->lockForUpdate()->first();

            if ($validated['amount'] > $sale->remaining_balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount cannot exceed the remaining balance of $' . number_format($sale->remaining_balance, 2),
                ]);
            }

            $nextRecId = (CustomerPayment::max('id') ?? 0) + 1;
            $paymentNumber = 'REC-' . date('Ym') . '-' . str_pad($nextRecId, 4, '0', STR_PAD_LEFT);

            $payment = CustomerPayment::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'user_id' => Auth::id(),
                'payment_number' => $paymentNumber,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update sale financial balance
            $newPaid = $sale->amount_paid + $validated['amount'];
            $newRemaining = max(0, $sale->grand_total - $newPaid);
            $newStatus = ($newRemaining == 0) ? 'PAID' : 'PARTIALLY_PAID';

            $sale->update([
                'amount_paid' => $newPaid,
                'remaining_balance' => $newRemaining,
                'payment_status' => $newStatus,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'CUSTOMER_PAYMENT_RECORD',
                'entity_type' => 'CustomerPayment',
                'entity_id' => $payment->id,
                'new_values' => $payment->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $payment;
        });

        return redirect()->route('payments.index')->with('success', 'Customer payment receipt ' . $payment->payment_number . ' recorded successfully.');
    }

    public function createSupplierPayment(Request $request)
    {
        $purchaseId = $request->query('purchase_id');
        $purchase = $purchaseId ? Purchase::with('supplier')->findOrFail($purchaseId) : null;
        $unpaidPurchases = Purchase::with('supplier')->where('remaining_balance', '>', 0)->orderBy('purchase_date', 'asc')->get();

        return view('payments.create_supplier', compact('purchase', 'unpaidPurchases'));
    }

    public function storeSupplierPayment(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => ['required', 'exists:purchases,id'],
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string'],
            'reference_number' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = DB::transaction(function () use ($validated, $request) {
            $purchase = Purchase::where('id', $validated['purchase_id'])->lockForUpdate()->first();

            if ($validated['amount'] > $purchase->remaining_balance) {
                throw ValidationException::withMessages([
                    'amount' => 'Payment amount cannot exceed the remaining supplier balance of $' . number_format($purchase->remaining_balance, 2),
                ]);
            }

            $nextSpayId = (SupplierPayment::max('id') ?? 0) + 1;
            $paymentNumber = 'SPAY-' . date('Ym') . '-' . str_pad($nextSpayId, 4, '0', STR_PAD_LEFT);

            $payment = SupplierPayment::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'user_id' => Auth::id(),
                'payment_number' => $paymentNumber,
                'payment_date' => $validated['payment_date'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update purchase balance
            $newPaid = $purchase->amount_paid + $validated['amount'];
            $newRemaining = max(0, $purchase->grand_total - $newPaid);
            $newStatus = ($newRemaining == 0) ? 'PAID' : 'PARTIALLY_PAID';

            $purchase->update([
                'amount_paid' => $newPaid,
                'remaining_balance' => $newRemaining,
                'payment_status' => $newStatus,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'SUPPLIER_PAYMENT_RECORD',
                'entity_type' => 'SupplierPayment',
                'entity_id' => $payment->id,
                'new_values' => $payment->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $payment;
        });

        return redirect()->route('payments.index', ['tab' => 'supplier'])->with('success', 'Supplier payment ' . $payment->payment_number . ' recorded successfully.');
    }
}
