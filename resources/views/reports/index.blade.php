<x-layouts.app>
    <x-slot:title>Business Reports</x-slot:title>
    <x-slot:headerTitle>Business Intelligence</x-slot:headerTitle>
    <x-slot:headerSubtitle>Reports, Profitability & Valuation</x-slot:headerSubtitle>

    <div class="space-y-6">
        <!-- Report Configuration Filter Card -->
        <div class="glass-card rounded-3xl p-6 border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Report Module</label>
                        <select name="type" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="sales" {{ $reportType === 'sales' ? 'selected' : '' }}>📈 Sales & Profit Margins</option>
                            <option value="purchases" {{ $reportType === 'purchases' ? 'selected' : '' }}>🚚 Purchases & Inbound Costs</option>
                            <option value="stock" {{ $reportType === 'stock' ? 'selected' : '' }}>📦 Stock Valuation & Levels</option>
                            <option value="debt" {{ $reportType === 'debt' ? 'selected' : '' }}>💳 Customer & Supplier Debt</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">From Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">To Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">
                        Generate Report
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold">
                        <i data-lucide="printer" class="w-4 h-4 inline-block"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- REPORT TYPE: SALES & PROFIT -->
        @if($reportType === 'sales' && $salesData)
            @php
                $totalRevenue = $salesData->sum('grand_total');
                $totalCollected = $salesData->sum('amount_paid');
                $totalCost = $salesData->sum('total_cost');
                $grossProfit = $totalRevenue - $totalCost;
                $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
            @endphp

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
                <div class="glass-card rounded-2xl p-5 border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Sales Revenue</span>
                    <p class="text-2xl font-black text-slate-900 mt-2">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Cash Collected</span>
                    <p class="text-2xl font-black text-emerald-600 mt-2">${{ number_format($totalCollected, 2) }}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Cost of Goods Sold (COGS)</span>
                    <p class="text-2xl font-black text-slate-700 mt-2">${{ number_format($totalCost, 2) }}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Gross Profit (Margin)</span>
                    <p class="text-2xl font-black text-indigo-600 mt-2">${{ number_format($grossProfit, 2) }} <span class="text-xs font-bold text-slate-500">({{ number_format($profitMargin, 1) }}%)</span></p>
                </div>
            </div>

            <!-- Sales Detailed Breakdown Table -->
            <div class="glass-card rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-6 py-4">Invoice #</th>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4 text-right">Revenue</th>
                                <th class="px-6 py-4 text-right">Cost (COGS)</th>
                                <th class="px-6 py-4 text-right">Gross Profit</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($salesData as $sale)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 font-mono font-bold text-slate-900 text-xs">{{ $sale->invoice_number }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-slate-800">{{ $sale->customer->name ?? 'Walk-In Customer' }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-slate-900">${{ number_format($sale->grand_total, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-slate-500">${{ number_format($sale->total_cost, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-emerald-600">${{ number_format($sale->gross_profit, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $sale->payment_status === 'PAID' ? 'badge-paid' : 'badge-unpaid' }}">
                                            {{ $sale->payment_status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-500">No sales transactions in this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- REPORT TYPE: STOCK VALUATION -->
        @if($reportType === 'stock' && $stockData)
            @php
                $valuationCost = $stockData->sum('stock_valuation_at_cost');
                $valuationRetail = $stockData->sum('stock_valuation_at_retail');
                $potentialProfit = $valuationRetail - $valuationCost;
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="glass-card rounded-2xl p-5 border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Stock Value (At Cost)</span>
                    <p class="text-2xl font-black text-slate-900 mt-2">${{ number_format($valuationCost, 2) }}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Stock Value (Retail)</span>
                    <p class="text-2xl font-black text-indigo-600 mt-2">${{ number_format($valuationRetail, 2) }}</p>
                </div>
                <div class="glass-card rounded-2xl p-5 border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Potential Inventory Margin</span>
                    <p class="text-2xl font-black text-emerald-600 mt-2">${{ number_format($potentialProfit, 2) }}</p>
                </div>
            </div>

            <div class="glass-card rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-6 py-4">Product Name & SKU</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4 text-center">Stock Level</th>
                                <th class="px-6 py-4 text-right">Unit Cost</th>
                                <th class="px-6 py-4 text-right">Selling Price</th>
                                <th class="px-6 py-4 text-right">Valuation (Cost)</th>
                                <th class="px-6 py-4 text-right">Valuation (Retail)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($stockData as $prod)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 font-medium text-slate-900">
                                        {{ $prod->name }}
                                        <span class="block font-mono text-xs text-slate-400">SKU: {{ $prod->sku }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ $prod->category->name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-center font-bold {{ $prod->current_stock <= $prod->minimum_stock_level ? 'text-amber-600' : 'text-slate-800' }}">
                                        {{ $prod->current_stock }} {{ $prod->unit }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-slate-600">${{ number_format($prod->cost_price, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-semibold text-slate-900">${{ number_format($prod->selling_price, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-slate-700">${{ number_format($prod->stock_valuation_at_cost, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-emerald-600">${{ number_format($prod->stock_valuation_at_retail, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
