<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryInfoController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'valuation');
        if (!in_array($tab, ['valuation', 'stock', 'invoices'])) {
            $tab = 'valuation';
        }

        // Global Store Valuation Totals
        $activeProductsQuery = Product::where('is_active', true);

        $totalBuyingPrice = (float) $activeProductsQuery->sum(DB::raw('current_stock * cost_price'));
        $totalSellingPrice = (float) $activeProductsQuery->sum(DB::raw('current_stock * selling_price'));
        $totalProjectedProfit = (float) ($totalSellingPrice - $totalBuyingPrice);
        $projectedMarginPercent = $totalSellingPrice > 0 ? (($totalProjectedProfit / $totalSellingPrice) * 100) : 0;

        $totalStockUnits = (int) $activeProductsQuery->sum('current_stock');
        $totalProductsCount = (int) $activeProductsQuery->count();
        $averageCostPrice = (float) ($activeProductsQuery->avg('cost_price') ?? 0);
        $averageSellingPrice = (float) ($activeProductsQuery->avg('selling_price') ?? 0);

        $lowStockCount = (int) Product::where('is_active', true)
            ->where('current_stock', '<=', DB::raw('minimum_stock_level'))
            ->where('current_stock', '>', 0)
            ->count();

        $outOfStockCount = (int) Product::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->count();

        $inStockCount = max(0, $totalProductsCount - ($lowStockCount + $outOfStockCount));

        $categories = Category::where('is_active', true)->orderBy('name', 'asc')->get();
        $allProducts = Product::orderBy('name', 'asc')->get();

        // Top 7 Products by Stock Quantity for visual Bar Graph
        $topStockProducts = Product::where('is_active', true)
            ->where('current_stock', '>', 0)
            ->orderBy('current_stock', 'desc')
            ->take(7)
            ->get(['name', 'current_stock', 'cost_price', 'selling_price']);

        // Stock Movements Flow Totals for Graph/Cards
        $totalPurchasesIn = (int) StockTransaction::where('type', 'PURCHASE')->sum('quantity');
        $totalSalesOut = abs((int) StockTransaction::where('type', 'SALE')->sum('quantity'));
        $totalAdjustmentsAdd = (int) StockTransaction::where('type', 'ADJUSTMENT_ADD')->sum('quantity');
        $totalAdjustmentsSub = abs((int) StockTransaction::where('type', 'ADJUSTMENT_SUB')->sum('quantity'));

        // 1. Valuation Tab Data
        $products = null;
        if ($tab === 'valuation') {
            $query = Product::with(['category', 'supplier'])->where('is_active', true);

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            if ($request->input('status') === 'low_stock') {
                $query->where('current_stock', '<=', DB::raw('minimum_stock_level'))->where('current_stock', '>', 0);
            } elseif ($request->input('status') === 'out_of_stock') {
                $query->where('current_stock', '<=', 0);
            } elseif ($request->input('status') === 'in_stock') {
                $query->where('current_stock', '>', DB::raw('minimum_stock_level'));
            }

            $sort = $request->input('sort', 'name');
            if ($sort === 'buying_val_desc') {
                $query->orderByRaw('(current_stock * cost_price) DESC');
            } elseif ($sort === 'selling_val_desc') {
                $query->orderByRaw('(current_stock * selling_price) DESC');
            } elseif ($sort === 'stock_desc') {
                $query->orderBy('current_stock', 'desc');
            } elseif ($sort === 'stock_asc') {
                $query->orderBy('current_stock', 'asc');
            } else {
                $query->orderBy('name', 'asc');
            }

            $products = $query->paginate(20)->withQueryString();
        }

        // 2. Stock Information Tab Data (Stock Movement Log)
        $stockTransactions = null;
        if ($tab === 'stock') {
            $stockQuery = StockTransaction::with(['product', 'user']);

            if ($request->filled('type')) {
                $stockQuery->where('type', $request->input('type'));
            }

            if ($request->filled('product_id')) {
                $stockQuery->where('product_id', $request->input('product_id'));
            }

            $stockTransactions = $stockQuery->latest('created_at')->paginate(15)->withQueryString();
        }

        // 3. Sales Invoices Tab Data
        $sales = null;
        $totalSalesCount = 0;
        $totalSalesRevenue = 0.0;
        $totalSalesDebt = 0.0;
        if ($tab === 'invoices') {
            $salesQuery = Sale::with(['customer', 'user']);

            if ($request->filled('search')) {
                $search = $request->input('search');
                $salesQuery->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('payment_status')) {
                $salesQuery->where('payment_status', $request->input('payment_status'));
            }

            $totalSalesCount = Sale::count();
            $totalSalesRevenue = (float) Sale::sum('grand_total');
            $totalSalesDebt = (float) Sale::sum('remaining_balance');

            $sales = $salesQuery->latest('sale_date')->paginate(15)->withQueryString();
        }

        return view('info.index', compact(
            'tab',
            'totalBuyingPrice',
            'totalSellingPrice',
            'totalProjectedProfit',
            'projectedMarginPercent',
            'totalStockUnits',
            'totalProductsCount',
            'averageCostPrice',
            'averageSellingPrice',
            'lowStockCount',
            'outOfStockCount',
            'inStockCount',
            'products',
            'categories',
            'allProducts',
            'topStockProducts',
            'totalPurchasesIn',
            'totalSalesOut',
            'totalAdjustmentsAdd',
            'totalAdjustmentsSub',
            'stockTransactions',
            'sales',
            'totalSalesCount',
            'totalSalesRevenue',
            'totalSalesDebt'
        ));
    }
}
