<x-layouts.app>
    <x-slot:title>Financial Report - Daily, Monthly & Yearly Analysis</x-slot:title>
    <x-slot:headerTitle>Financial Reports</x-slot:headerTitle>
    <x-slot:headerSubtitle>Daily, Monthly & Yearly Profitability Intelligence</x-slot:headerSubtitle>

    <div class="space-y-6" x-data="{ currentPeriod: '{{ $period ?? 'daily' }}' }">

        <!-- Top Header & Section Badge -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-purple-50 text-purple-800 border border-purple-300">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-purple-600"></i>
                    <span>Section 5 &bull; Financial Statements</span>
                </span>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1.5">Business Financial Reports</h1>
                <p class="text-xs text-slate-600 font-semibold mt-0.5">Track revenue, cost of goods, gross profits, and cash receipts across daily, monthly, and yearly timelines.</p>
            </div>

            <!-- Print & Quick Actions -->
            <div class="flex items-center gap-2.5">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border-2 border-slate-300 hover:bg-slate-50 text-slate-800 text-xs font-black shadow-xs transition-all cursor-pointer">
                    <i data-lucide="printer" class="w-4 h-4 text-slate-600"></i>
                    <span>Print Financial Statement</span>
                </button>
            </div>
        </div>

        <!-- Period Selector Card -->
        <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                
                <!-- Period Toggle Tabs -->
                <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
                    <a href="{{ route('reports.financial', ['period' => 'daily', 'date' => $date]) }}" 
                       class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black transition-all {{ $period === 'daily' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        <span>Daily Report</span>
                    </a>

                    <a href="{{ route('reports.financial', ['period' => 'monthly', 'month' => $month]) }}" 
                       class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black transition-all {{ $period === 'monthly' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                        <span>Monthly Report</span>
                    </a>

                    <a href="{{ route('reports.financial', ['period' => 'yearly', 'year' => $year]) }}" 
                       class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black transition-all {{ $period === 'yearly' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        <i data-lucide="calendar-range" class="w-3.5 h-3.5"></i>
                        <span>Yearly Report</span>
                    </a>

                    <a href="{{ route('reports.financial', ['period' => 'custom', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                       class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black transition-all {{ $period === 'custom' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                        <span>Custom Range</span>
                    </a>
                </div>

                <!-- Date Filter Input Specific to Active Period -->
                <form method="GET" action="{{ route('reports.financial') }}" class="flex items-center gap-2">
                    <input type="hidden" name="period" value="{{ $period }}">

                    @if($period === 'daily')
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-600">Select Date:</label>
                            <input type="date" name="date" value="{{ $date }}" class="px-3 py-1.5 rounded-xl bg-slate-50 border-2 border-slate-300 text-xs font-bold text-slate-800 focus:bg-white focus:border-purple-600 focus:outline-none">
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                                Update
                            </button>
                        </div>
                    @elseif($period === 'monthly')
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-600">Select Month:</label>
                            <input type="month" name="month" value="{{ $month }}" class="px-3 py-1.5 rounded-xl bg-slate-50 border-2 border-slate-300 text-xs font-bold text-slate-800 focus:bg-white focus:border-purple-600 focus:outline-none">
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                                Update
                            </button>
                        </div>
                    @elseif($period === 'yearly')
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-bold text-slate-600">Select Year:</label>
                            <input type="number" min="2020" max="2035" name="year" value="{{ $year }}" class="w-24 px-3 py-1.5 rounded-xl bg-slate-50 border-2 border-slate-300 text-xs font-bold text-slate-800 focus:bg-white focus:border-purple-600 focus:outline-none">
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                                Update
                            </button>
                        </div>
                    @elseif($period === 'custom')
                        <div class="flex items-center gap-2 flex-wrap">
                            <label class="text-xs font-bold text-slate-600">From:</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-1.5 rounded-xl bg-slate-50 border-2 border-slate-300 text-xs font-bold text-slate-800 focus:outline-none">
                            <label class="text-xs font-bold text-slate-600">To:</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-1.5 rounded-xl bg-slate-50 border-2 border-slate-300 text-xs font-bold text-slate-800 focus:outline-none">
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                                Update
                            </button>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Active Period Statement Summary Banner -->
            <div class="pt-4 flex flex-wrap items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="font-extrabold text-slate-500 uppercase tracking-wider">Report For:</span>
                    <span class="font-black text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg">
                        @if($period === 'daily')
                            📅 {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                        @elseif($period === 'monthly')
                            📆 {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                        @elseif($period === 'yearly')
                            🗓️ Calendar Year {{ $year }}
                        @else
                            🔍 {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} &rarr; {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                        @endif
                    </span>
                </div>

                <div class="text-slate-500 font-semibold">
                    Invoices Logged: <strong class="text-slate-900">{{ $metrics['invoices_count'] }}</strong> | 
                    Total Items Sold: <strong class="text-slate-900">{{ $metrics['items_sold_count'] }}</strong>
                </div>
            </div>
        </div>

        <!-- 4 Financial KPI Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Gross Sales Revenue -->
            <div class="glass-card rounded-2xl p-5 border-2 border-slate-200 bg-white shadow-xs">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500">Gross Sales Revenue</span>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">{{ number_format($metrics['gross_sales'], 2) }} <span class="text-xs font-bold text-slate-500">ETB</span></p>
                <div class="flex items-center gap-1.5 mt-2 text-xs font-bold text-slate-500">
                    <i data-lucide="receipt" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>{{ $metrics['invoices_count'] }} orders completed</span>
                </div>
            </div>

            <!-- Cost of Goods Sold (COGS) -->
            <div class="glass-card rounded-2xl p-5 border-2 border-rose-200 bg-gradient-to-br from-rose-50/40 to-white shadow-xs">
                <span class="text-xs font-black uppercase tracking-wider text-rose-800">Cost of Goods Sold (COGS)</span>
                <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">{{ number_format($metrics['cost_of_goods'], 2) }} <span class="text-xs font-bold text-slate-500">ETB</span></p>
                <div class="flex items-center gap-1.5 mt-2 text-xs font-bold text-rose-700">
                    <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                    <span>Inventory purchase cost</span>
                </div>
            </div>

            <!-- Gross Profit & Margin % -->
            <div class="glass-card rounded-2xl p-5 border-2 border-emerald-200 bg-gradient-to-br from-emerald-50/40 to-white shadow-xs">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-800">Gross Profit (Net Margin)</span>
                <p class="text-2xl sm:text-3xl font-black text-emerald-700 mt-2">{{ number_format($metrics['gross_profit'], 2) }} <span class="text-xs font-bold text-slate-500">ETB</span></p>
                <div class="flex items-center gap-1.5 mt-2 text-xs font-black text-emerald-800">
                    <i data-lucide="percent" class="w-3.5 h-3.5"></i>
                    <span>Margin: {{ number_format($metrics['profit_margin'], 1) }}% of revenue</span>
                </div>
            </div>

            <!-- Cash Inflow vs Receivables -->
            <div class="glass-card rounded-2xl p-5 border-2 border-indigo-200 bg-gradient-to-br from-indigo-50/40 to-white shadow-xs">
                <span class="text-xs font-black uppercase tracking-wider text-indigo-800">Cash Received / Inflow</span>
                <p class="text-2xl sm:text-3xl font-black text-indigo-700 mt-2">{{ number_format($metrics['cash_collected'], 2) }} <span class="text-xs font-bold text-slate-500">ETB</span></p>
                <div class="flex items-center justify-between mt-2 text-xs font-bold">
                    <span class="text-slate-500">Unpaid / Debt:</span>
                    <span class="text-amber-700 font-black">{{ number_format($metrics['unpaid_balance'], 2) }} ETB</span>
                </div>
            </div>
        </div>

        <!-- BREAKDOWN SECTION BY PERIOD -->

        <!-- A. MONTHLY BREAKDOWN: Day-by-Day Financial Ledger -->
        @if($period === 'monthly' && count($breakdownRows) > 0)
            <div class="glass-card rounded-3xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Day-by-Day Financial Breakdown ({{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }})</h2>
                        <p class="text-xs text-slate-500 font-semibold">Every day's sales revenue, cost of goods, gross profits, and cash collected.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider font-black border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5">Date</th>
                                <th class="px-4 py-3.5 text-center">Orders</th>
                                <th class="px-4 py-3.5 text-right font-extrabold">Gross Revenue</th>
                                <th class="px-4 py-3.5 text-right font-extrabold text-rose-800">Cost (COGS)</th>
                                <th class="px-4 py-3.5 text-right font-extrabold text-emerald-800">Gross Profit</th>
                                <th class="px-4 py-3.5 text-right">Margin %</th>
                                <th class="px-4 py-3.5 text-right">Cash Received</th>
                                <th class="px-4 py-3.5 text-right">Unpaid Balance</th>
                                <th class="px-4 py-3.5 text-center">View Day</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($breakdownRows as $row)
                                <tr class="hover:bg-slate-50 transition-colors {{ $row['invoices_count'] > 0 ? 'bg-white' : 'bg-slate-50/40 opacity-75' }}">
                                    <td class="px-5 py-3 font-bold text-slate-800">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-black">
                                        {{ $row['invoices_count'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-slate-900">
                                        {{ number_format($row['gross_sales'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-600">
                                        {{ number_format($row['cost_of_goods'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3 text-right font-black {{ $row['gross_profit'] > 0 ? 'text-emerald-700' : 'text-slate-500' }}">
                                        {{ number_format($row['gross_profit'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-700">
                                        {{ number_format($row['profit_margin'], 1) }}%
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-indigo-700">
                                        {{ number_format($row['cash_collected'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-amber-700">
                                        {{ number_format($row['unpaid_balance'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($row['invoices_count'] > 0)
                                            <a href="{{ route('reports.financial', ['period' => 'daily', 'date' => $row['full_date']]) }}" 
                                               class="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-[11px] transition-colors">
                                                Day Invoices &rarr;
                                            </a>
                                        @else
                                            <span class="text-slate-300 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- B. YEARLY BREAKDOWN: Month-by-Month Financial Ledger -->
        @elseif($period === 'yearly' && count($breakdownRows) > 0)
            <div class="glass-card rounded-3xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Month-by-Month Annual Financial Ledger (Year {{ $year }})</h2>
                        <p class="text-xs text-slate-500 font-semibold">Comparison of monthly sales revenue, cost of goods, and gross margin across all 12 months.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider font-black border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5">Month</th>
                                <th class="px-4 py-3.5 text-center">Invoices</th>
                                <th class="px-4 py-3.5 text-right font-extrabold">Gross Revenue</th>
                                <th class="px-4 py-3.5 text-right font-extrabold text-rose-800">Cost (COGS)</th>
                                <th class="px-4 py-3.5 text-right font-extrabold text-emerald-800">Gross Profit</th>
                                <th class="px-4 py-3.5 text-right">Margin %</th>
                                <th class="px-4 py-3.5 text-right">Cash Collected</th>
                                <th class="px-4 py-3.5 text-right">Outstanding Credit</th>
                                <th class="px-4 py-3.5 text-center">View Month</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($breakdownRows as $row)
                                <tr class="hover:bg-slate-50 transition-colors {{ $row['invoices_count'] > 0 ? 'bg-white' : 'bg-slate-50/40 opacity-75' }}">
                                    <td class="px-5 py-3.5 font-black text-slate-900 text-sm">
                                        {{ $row['label'] }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-black">
                                        {{ $row['invoices_count'] }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-black text-slate-900">
                                        {{ number_format($row['gross_sales'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-slate-600">
                                        {{ number_format($row['cost_of_goods'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-black {{ $row['gross_profit'] > 0 ? 'text-emerald-700' : 'text-slate-500' }}">
                                        {{ number_format($row['gross_profit'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-slate-700">
                                        {{ number_format($row['profit_margin'], 1) }}%
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-indigo-700">
                                        {{ number_format($row['cash_collected'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-amber-700">
                                        {{ number_format($row['unpaid_balance'], 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if($row['invoices_count'] > 0)
                                            <a href="{{ route('reports.financial', ['period' => 'monthly', 'month' => $row['month_key']]) }}" 
                                               class="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-[11px] transition-colors">
                                                Month Days &rarr;
                                            </a>
                                        @else
                                            <span class="text-slate-300 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- C. DAILY / CUSTOM: Invoices Transaction Detail List -->
        @else
            <div class="glass-card rounded-3xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">
                            @if($period === 'daily')
                                Daily Invoices Transaction Ledger ({{ \Carbon\Carbon::parse($date)->format('M d, Y') }})
                            @else
                                Custom Period Invoices Ledger
                            @endif
                        </h2>
                        <p class="text-xs text-slate-500 font-semibold">Every sales invoice issued with item lines, profit margin, and payment collection.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider font-black border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5">Invoice #</th>
                                <th class="px-4 py-3.5">Customer</th>
                                <th class="px-4 py-3.5 text-center">Items</th>
                                <th class="px-4 py-3.5 text-right font-extrabold">Revenue</th>
                                <th class="px-4 py-3.5 text-right font-extrabold text-rose-800">Cost</th>
                                <th class="px-4 py-3.5 text-right font-extrabold text-emerald-800">Gross Profit</th>
                                <th class="px-4 py-3.5 text-right">Amount Paid</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <th class="px-4 py-3.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($salesList as $sale)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3.5 font-mono font-black text-slate-900 text-sm">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-slate-800">
                                        {{ $sale->customer->name ?? 'Walk-In Customer' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-bold text-slate-600">
                                        {{ $sale->items->sum('quantity') }}
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-black text-slate-900">
                                        {{ number_format($sale->grand_total, 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-slate-600">
                                        {{ number_format($sale->total_cost, 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-black text-emerald-700">
                                        +{{ number_format($sale->gross_profit, 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-right font-bold text-indigo-700">
                                        {{ number_format($sale->amount_paid, 2) }} ETB
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black {{ $sale->payment_status === 'PAID' ? 'bg-emerald-100 text-emerald-800' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $sale->payment_status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs" title="View Invoice">
                                            View &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-slate-400 font-semibold">
                                        No sales transactions found for this date.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Top Selling Products in Period -->
        @if($topProducts->count() > 0)
            <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <i data-lucide="award" class="w-4 h-4 text-amber-500"></i>
                        <span>Top Selling Products in This Period</span>
                    </h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-3">
                    @foreach($topProducts as $tp)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                            <p class="text-xs font-black text-slate-900 truncate">{{ $tp->product->name ?? 'Product #' . $tp->product_id }}</p>
                            <div class="flex items-center justify-between mt-1 text-[11px]">
                                <span class="font-bold text-slate-500">{{ $tp->total_qty }} units sold</span>
                                <span class="font-black text-emerald-700">{{ number_format($tp->total_revenue, 2) }} ETB</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>
