<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Financial KPIs
        $todaySales = Sale::whereDate('sale_date', $today)->sum('grand_total');
        $todaySalesCount = Sale::whereDate('sale_date', $today)->count();
        $todayCashCollected = Sale::whereDate('sale_date', $today)->sum('amount_paid');
        $totalCustomerReceivables = Sale::sum('remaining_balance');
        $totalSupplierPayables = Purchase::sum('remaining_balance');

        // Inventory KPIs
        $totalProductsCount = Product::where('is_active', true)->count();
        $totalStockUnits = Product::where('is_active', true)->sum('current_stock');
        $valuationAtCost = Product::where('is_active', true)->sum(DB::raw('current_stock * cost_price'));
        $valuationAtRetail = Product::where('is_active', true)->sum(DB::raw('current_stock * selling_price'));

        // Low & Out of Stock counts
        $lowStockCount = Product::where('is_active', true)
            ->where('current_stock', '<=', DB::raw('minimum_stock_level'))
            ->where('current_stock', '>', 0)
            ->count();

        $outOfStockCount = Product::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->count();

        // Low Stock Products list
        $lowStockProducts = Product::with(['category', 'supplier'])
            ->where('current_stock', '<=', DB::raw('minimum_stock_level'))
            ->where('is_active', true)
            ->orderBy('current_stock', 'asc')
            ->take(6)
            ->get();

        // Recent Sales
        $recentSales = Sale::with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Recent Stock Transactions
        $recentTransactions = StockTransaction::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 7-day Sales Trend (Chart Data)
        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayTotal = Sale::whereDate('sale_date', $date)->sum('grand_total');
            $salesTrend[] = [
                'day' => $date->format('D'),
                'date' => $date->format('M d'),
                'total' => (float)$dayTotal
            ];
        }

        // Top Customers with Balances
        $customersWithDebt = Sale::with('customer')
            ->where('remaining_balance', '>', 0)
            ->select('customer_id', DB::raw('SUM(remaining_balance) as total_debt'), DB::raw('COUNT(id) as unpaid_invoices'))
            ->groupBy('customer_id')
            ->orderByDesc('total_debt')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todaySalesCount',
            'todayCashCollected',
            'totalCustomerReceivables',
            'totalSupplierPayables',
            'totalProductsCount',
            'totalStockUnits',
            'valuationAtCost',
            'valuationAtRetail',
            'lowStockCount',
            'outOfStockCount',
            'lowStockProducts',
            'recentSales',
            'recentTransactions',
            'salesTrend',
            'customersWithDebt'
        ));
    }
}
