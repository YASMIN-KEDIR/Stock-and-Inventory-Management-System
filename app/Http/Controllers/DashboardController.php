<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
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
    public function index(Request $request)
    {
        $today = Carbon::today();

        // 1. Core Financial KPIs
        $todaySales = (float) Sale::whereDate('sale_date', $today)->sum('grand_total');
        $todaySalesCount = (int) Sale::whereDate('sale_date', $today)->count();
        $todayCashCollected = (float) Sale::whereDate('sale_date', $today)->sum('amount_paid');
        $totalCustomerReceivables = (float) Sale::sum('remaining_balance');
        $totalSupplierPayables = (float) Purchase::sum('remaining_balance');

        // 2. Financial Breakdown by Channel (Cash, Telebirr, CBE, Credits)
        // All-Time Collections by Channel:
        $allTimeCash = (float) CustomerPayment::where('payment_method', 'CASH')->sum('amount');
        $allTimeTelebirr = (float) CustomerPayment::whereIn('payment_method', ['TELEBIRR', 'TELE'])->sum('amount');
        $allTimeCbe = (float) CustomerPayment::whereIn('payment_method', ['CBE', 'CBE_BIRR'])->sum('amount');
        $allTimeOtherBank = (float) CustomerPayment::whereIn('payment_method', ['BANK_TRANSFER', 'BANK', 'POS_CARD'])->sum('amount');
        $allTimeCollected = $allTimeCash + $allTimeTelebirr + $allTimeCbe + $allTimeOtherBank;

        // Overall finance currently in the system:
        // Cash collected (Cash + Telebirr + CBE + Bank) + Active Unpaid Customer Credits
        $overallFinance = $allTimeCollected + $totalCustomerReceivables;

        // Today's Collections by Channel:
        $todayCash = (float) CustomerPayment::whereDate('payment_date', $today)->where('payment_method', 'CASH')->sum('amount');
        $todayTelebirr = (float) CustomerPayment::whereDate('payment_date', $today)->whereIn('payment_method', ['TELEBIRR', 'TELE'])->sum('amount');
        $todayCbe = (float) CustomerPayment::whereDate('payment_date', $today)->whereIn('payment_method', ['CBE', 'CBE_BIRR'])->sum('amount');
        $todayOtherBank = (float) CustomerPayment::whereDate('payment_date', $today)->whereIn('payment_method', ['BANK_TRANSFER', 'BANK', 'POS_CARD'])->sum('amount');
        $todayCredits = (float) Sale::whereDate('sale_date', $today)->sum('remaining_balance');

        // 3. Inventory KPIs
        $totalProductsCount = (int) Product::where('is_active', true)->count();
        $totalStockUnits = (int) Product::where('is_active', true)->sum('current_stock');
        $valuationAtCost = (float) Product::where('is_active', true)->sum(DB::raw('current_stock * cost_price'));
        $valuationAtRetail = (float) Product::where('is_active', true)->sum(DB::raw('current_stock * selling_price'));

        // 4. Low & Out of Stock counts
        $lowStockCount = (int) Product::where('is_active', true)
            ->where('current_stock', '<=', DB::raw('minimum_stock_level'))
            ->where('current_stock', '>', 0)
            ->count();

        $outOfStockCount = (int) Product::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->count();

        $lowStockProducts = Product::with(['category', 'supplier'])
            ->where('current_stock', '<=', DB::raw('minimum_stock_level'))
            ->where('is_active', true)
            ->orderBy('current_stock', 'asc')
            ->take(6)
            ->get();

        // 5. Recent Sales
        $recentSales = Sale::with(['customer', 'user', 'payments'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 6. 7-day Sales Trend
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

        // 7. Top Customers with Balances / Debts
        $customersWithDebt = Sale::with('customer')
            ->where('remaining_balance', '>', 0)
            ->select('customer_id', DB::raw('SUM(remaining_balance) as total_debt'), DB::raw('COUNT(id) as unpaid_invoices'))
            ->groupBy('customer_id')
            ->orderByDesc('total_debt')
            ->take(5)
            ->get();

        // 8. Universal Smart Search Processing (Customer Name, Product Name, Date)
        $searchQuery = trim($request->input('q', ''));
        $searchDate = $request->input('search_date');
        $searchMode = $request->input('search_mode', 'all');

        $customerSearchResults = collect();
        $productSearchResults = collect();
        $dateSalesResults = collect();
        $dateSummary = null;

        $hasSearch = !empty($searchQuery) || !empty($searchDate);

        if ($hasSearch) {
            // A. Search by Date
            if (!empty($searchDate)) {
                $targetDate = Carbon::parse($searchDate);
                $dateSalesResults = Sale::with(['customer', 'items.product', 'payments'])
                    ->whereDate('sale_date', $targetDate)
                    ->orderBy('id', 'desc')
                    ->get();

                $dateSummary = [
                    'date' => $targetDate->format('M d, Y'),
                    'total_sales' => (float) $dateSalesResults->sum('grand_total'),
                    'invoices_count' => (int) $dateSalesResults->count(),
                    'cash_collected' => (float) $dateSalesResults->sum('amount_paid'),
                    'credit_issued' => (float) $dateSalesResults->sum('remaining_balance'),
                    'cash' => (float) CustomerPayment::whereDate('payment_date', $targetDate)->where('payment_method', 'CASH')->sum('amount'),
                    'telebirr' => (float) CustomerPayment::whereDate('payment_date', $targetDate)->whereIn('payment_method', ['TELEBIRR', 'TELE'])->sum('amount'),
                    'cbe' => (float) CustomerPayment::whereDate('payment_date', $targetDate)->whereIn('payment_method', ['CBE', 'CBE_BIRR'])->sum('amount'),
                ];
            }

            // B. Search by Customer Name / Phone
            if (!empty($searchQuery) && in_array($searchMode, ['all', 'customer'])) {
                $customerSearchResults = Customer::where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('phone', 'like', "%{$searchQuery}%")
                    ->orWhere('email', 'like', "%{$searchQuery}%")
                    ->with(['sales' => function ($q) {
                        $q->latest()->take(10);
                    }, 'payments'])
                    ->take(6)
                    ->get();
            }

            // C. Search by Product Name / SKU / Barcode
            if (!empty($searchQuery) && in_array($searchMode, ['all', 'product'])) {
                $productSearchResults = Product::where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('sku', 'like', "%{$searchQuery}%")
                    ->orWhere('barcode', 'like', "%{$searchQuery}%")
                    ->with(['category', 'supplier', 'saleItems'])
                    ->take(6)
                    ->get();
            }
        }

        return view('dashboard', compact(
            'todaySales',
            'todaySalesCount',
            'todayCashCollected',
            'totalCustomerReceivables',
            'totalSupplierPayables',
            'allTimeCash',
            'allTimeTelebirr',
            'allTimeCbe',
            'allTimeOtherBank',
            'allTimeCollected',
            'overallFinance',
            'todayCash',
            'todayTelebirr',
            'todayCbe',
            'todayOtherBank',
            'todayCredits',
            'totalProductsCount',
            'totalStockUnits',
            'valuationAtCost',
            'valuationAtRetail',
            'lowStockCount',
            'outOfStockCount',
            'lowStockProducts',
            'recentSales',
            'salesTrend',
            'customersWithDebt',
            'hasSearch',
            'searchQuery',
            'searchDate',
            'searchMode',
            'customerSearchResults',
            'productSearchResults',
            'dateSalesResults',
            'dateSummary'
        ));
    }
}
