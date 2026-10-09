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
        $todayCashCollected = Sale::whereDate('sale_date', $today)->sum('amount_paid');
        $totalCustomerReceivables = Sale::sum('remaining_balance');
        $totalSupplierPayables = Purchase::sum('remaining_balance');

        // Inventory Valuation KPIs
        $totalStockUnits = Product::where('is_active', true)->sum('current_stock');
        $valuationAtCost = Product::where('is_active', true)->sum(DB::raw('current_stock * cost_price'));
        $valuationAtRetail = Product::where('is_active', true)->sum(DB::raw('current_stock * selling_price'));

        // Low Stock Products
        $lowStockProducts = Product::with(['category', 'supplier'])
            ->where('current_stock', '<=', DB::raw('minimum_stock_level'))
            ->where('is_active', true)
            ->orderBy('current_stock', 'asc')
            ->take(8)
            ->get();

        // Recent Activity Streams
        $recentSales = Sale::with(['customer', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $recentPurchases = Purchase::with(['supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $recentTransactions = StockTransaction::with(['product', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Top 5 Selling Products by quantity
        $topProducts = Product::where('is_active', true)
            ->orderBy('current_stock', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todayCashCollected',
            'totalCustomerReceivables',
            'totalSupplierPayables',
            'totalStockUnits',
            'valuationAtCost',
            'valuationAtRetail',
            'lowStockProducts',
            'recentSales',
            'recentPurchases',
            'recentTransactions',
            'topProducts'
        ));
    }
}
