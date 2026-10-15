<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'daily');
        if (!in_array($period, ['daily', 'monthly', 'yearly', 'custom'])) {
            $period = 'daily';
        }

        $today = Carbon::today();
        $date = $request->input('date', $today->toDateString());
        $month = $request->input('month', $today->format('Y-m'));
        $year = $request->input('year', $today->format('Y'));
        $startDate = $request->input('start_date', $today->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', $today->toDateString());

        // Report Data Containers
        $metrics = [
            'gross_sales' => 0.0,
            'cost_of_goods' => 0.0,
            'gross_profit' => 0.0,
            'profit_margin' => 0.0,
            'cash_collected' => 0.0,
            'unpaid_balance' => 0.0,
            'invoices_count' => 0,
            'items_sold_count' => 0,
        ];

        $breakdownRows = [];
        $salesList = collect();
        $topProducts = collect();

        if ($period === 'daily') {
            $parsedDate = Carbon::parse($date);
            $sales = Sale::with(['customer', 'items.product', 'user'])
                ->whereDate('sale_date', $parsedDate)
                ->orderBy('id', 'desc')
                ->get();

            $salesList = $sales;
            $metrics = $this->calculateMetrics($sales);

            // Top Products sold today
            $topProducts = SaleItem::whereHas('sale', function ($q) use ($parsedDate) {
                    $q->whereDate('sale_date', $parsedDate);
                })
                ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                ->with('product')
                ->groupBy('product_id')
                ->orderByDesc('total_revenue')
                ->take(5)
                ->get();

        } elseif ($period === 'monthly') {
            $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
            $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();

            $sales = Sale::with(['customer', 'items.product', 'user'])
                ->whereBetween('sale_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->orderBy('sale_date', 'asc')
                ->get();

            $salesList = $sales;
            $metrics = $this->calculateMetrics($sales);

            // Day-by-Day Financial Breakdown Table for the Month
            $daysInMonth = $startOfMonth->daysInMonth;
            $salesByDate = $sales->groupBy(function ($sale) {
                return $sale->sale_date->format('Y-m-d');
            });

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $curDate = Carbon::createFromDate($startOfMonth->year, $startOfMonth->month, $d)->toDateString();
                $daySales = $salesByDate->get($curDate, collect());
                $dayMetrics = $this->calculateMetrics($daySales);

                $breakdownRows[] = [
                    'label' => Carbon::parse($curDate)->format('M d, D'),
                    'full_date' => $curDate,
                    'invoices_count' => $dayMetrics['invoices_count'],
                    'gross_sales' => $dayMetrics['gross_sales'],
                    'cost_of_goods' => $dayMetrics['cost_of_goods'],
                    'gross_profit' => $dayMetrics['gross_profit'],
                    'profit_margin' => $dayMetrics['profit_margin'],
                    'cash_collected' => $dayMetrics['cash_collected'],
                    'unpaid_balance' => $dayMetrics['unpaid_balance'],
                ];
            }

            // Top Products for the Month
            $topProducts = SaleItem::whereHas('sale', function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('sale_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
                })
                ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                ->with('product')
                ->groupBy('product_id')
                ->orderByDesc('total_revenue')
                ->take(5)
                ->get();

        } elseif ($period === 'yearly') {
            $startOfYear = Carbon::createFromDate((int)$year, 1, 1)->startOfYear();
            $endOfYear = Carbon::createFromDate((int)$year, 12, 31)->endOfYear();

            $sales = Sale::with(['customer', 'items.product', 'user'])
                ->whereBetween('sale_date', [$startOfYear->toDateString(), $endOfYear->toDateString()])
                ->orderBy('sale_date', 'asc')
                ->get();

            $salesList = $sales;
            $metrics = $this->calculateMetrics($sales);

            // Month-by-Month Financial Breakdown Table (1 to 12)
            $salesByMonth = $sales->groupBy(function ($sale) {
                return (int) $sale->sale_date->format('n');
            });

            for ($m = 1; $m <= 12; $m++) {
                $monthSales = $salesByMonth->get($m, collect());
                $mMetrics = $this->calculateMetrics($monthSales);
                $monthDate = Carbon::createFromDate((int)$year, $m, 1);

                $breakdownRows[] = [
                    'label' => $monthDate->format('F Y'),
                    'month_key' => $monthDate->format('Y-m'),
                    'invoices_count' => $mMetrics['invoices_count'],
                    'gross_sales' => $mMetrics['gross_sales'],
                    'cost_of_goods' => $mMetrics['cost_of_goods'],
                    'gross_profit' => $mMetrics['gross_profit'],
                    'profit_margin' => $mMetrics['profit_margin'],
                    'cash_collected' => $mMetrics['cash_collected'],
                    'unpaid_balance' => $mMetrics['unpaid_balance'],
                ];
            }

            // Top Products for the Year
            $topProducts = SaleItem::whereHas('sale', function ($q) use ($startOfYear, $endOfYear) {
                    $q->whereBetween('sale_date', [$startOfYear->toDateString(), $endOfYear->toDateString()]);
                })
                ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                ->with('product')
                ->groupBy('product_id')
                ->orderByDesc('total_revenue')
                ->take(5)
                ->get();

        } elseif ($period === 'custom') {
            $sales = Sale::with(['customer', 'items.product', 'user'])
                ->whereBetween('sale_date', [$startDate, $endDate])
                ->orderBy('sale_date', 'desc')
                ->get();

            $salesList = $sales;
            $metrics = $this->calculateMetrics($sales);

            $topProducts = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('sale_date', [$startDate, $endDate]);
                })
                ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                ->with('product')
                ->groupBy('product_id')
                ->orderByDesc('total_revenue')
                ->take(5)
                ->get();
        }

        return view('reports.financial', compact(
            'period',
            'date',
            'month',
            'year',
            'startDate',
            'endDate',
            'metrics',
            'breakdownRows',
            'salesList',
            'topProducts'
        ));
    }

    private function calculateMetrics($sales): array
    {
        $grossSales = (float) $sales->sum('grand_total');
        $costOfGoods = (float) $sales->sum('total_cost');
        $grossProfit = (float) ($grossSales - $costOfGoods);
        $profitMargin = $grossSales > 0 ? (($grossProfit / $grossSales) * 100) : 0;
        $cashCollected = (float) $sales->sum('amount_paid');
        $unpaidBalance = (float) $sales->sum('remaining_balance');
        $invoicesCount = (int) $sales->count();
        $itemsSoldCount = (int) $sales->flatMap->items->sum('quantity');

        return [
            'gross_sales' => $grossSales,
            'cost_of_goods' => $costOfGoods,
            'gross_profit' => $grossProfit,
            'profit_margin' => $profitMargin,
            'cash_collected' => $cashCollected,
            'unpaid_balance' => $unpaidBalance,
            'invoices_count' => $invoicesCount,
            'items_sold_count' => $itemsSoldCount,
        ];
    }
}
