<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $reportType = $request->input('type', 'sales');

        $salesData = null;
        $purchasesData = null;
        $stockData = null;
        $debtData = null;

        if ($reportType === 'sales') {
            $salesData = Sale::with(['customer', 'items.product'])
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->orderBy('sale_date', 'desc')
                ->get();
        } elseif ($reportType === 'purchases') {
            $purchasesData = Purchase::with(['supplier', 'items.product'])
                ->whereBetween('purchase_date', [$startDate, $endDate])
                ->orderBy('purchase_date', 'desc')
                ->get();
        } elseif ($reportType === 'stock') {
            $stockData = Product::with(['category', 'supplier'])->where('is_active', true)->orderBy('name', 'asc')->get();
        } elseif ($reportType === 'debt') {
            $debtData = [
                'customers' => Customer::whereHas('sales', function ($q) {
                    $q->where('remaining_balance', '>', 0);
                })->with(['sales' => function ($q) {
                    $q->where('remaining_balance', '>', 0);
                }])->get(),
                'suppliers' => Supplier::whereHas('purchases', function ($q) {
                    $q->where('remaining_balance', '>', 0);
                })->with(['purchases' => function ($q) {
                    $q->where('remaining_balance', '>', 0);
                }])->get(),
            ];
        }

        return view('reports.index', compact('startDate', 'endDate', 'reportType', 'salesData', 'purchasesData', 'stockData', 'debtData'));
    }
}
