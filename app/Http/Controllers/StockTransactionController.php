<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['product', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        $transactions = $query->latest('created_at')->paginate(15)->withQueryString();
        $products = Product::orderBy('name', 'asc')->get();

        return view('stock_transactions.index', compact('transactions', 'products'));
    }

    public function createAdjustment()
    {
        $products = Product::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('stock_transactions.create_adjustment', compact('products'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'adjustment_type' => ['required', 'in:ADDITION,SUBTRACTION'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'min:5'],
        ]);

        $transaction = DB::transaction(function () use ($validated, $request) {
            $product = Product::where('id', $validated['product_id'])->lockForUpdate()->first();
            $balanceBefore = $product->current_stock;

            if ($validated['adjustment_type'] === 'SUBTRACTION') {
                if ($balanceBefore < $validated['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => "Cannot subtract {$validated['quantity']} units. Only {$balanceBefore} available in stock."
                    ]);
                }
                $signedQty = -$validated['quantity'];
                $balanceAfter = $balanceBefore - $validated['quantity'];
                $txType = 'ADJUSTMENT_SUB';
            } else {
                $signedQty = $validated['quantity'];
                $balanceAfter = $balanceBefore + $validated['quantity'];
                $txType = 'ADJUSTMENT_ADD';
            }

            // Update physical stock
            $product->update(['current_stock' => $balanceAfter]);

            // Append immutable ledger movement
            $tx = StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $txType,
                'quantity' => $signedQty,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'reference_type' => 'ManualAdjustment',
                'reference_id' => null,
                'reason' => $validated['reason'],
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'STOCK_MANUAL_ADJUSTMENT',
                'entity_type' => 'StockTransaction',
                'entity_id' => $tx->id,
                'old_values' => ['current_stock' => $balanceBefore],
                'new_values' => ['current_stock' => $balanceAfter, 'reason' => $validated['reason']],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $tx;
        });

        return redirect()->route('stock-transactions.index')->with('success', 'Stock adjustment recorded successfully.');
    }
}
