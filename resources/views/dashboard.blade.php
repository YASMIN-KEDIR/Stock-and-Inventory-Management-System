<x-layouts.app>
    <x-slot:title>Store Summary</x-slot:title>
    <x-slot:headerTitle>Store Summary</x-slot:headerTitle>
    <x-slot:headerSubtitle>Overview & Analytics</x-slot:headerSubtitle>

    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Welcome Banner & Quick Action Toolbar -->
        <div class="glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-xs bg-white flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Shop Status: Active
                    </span>
                    <span class="text-xs text-slate-400 font-medium">&bull; {{ date('l, F j, Y') }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Store Performance Overview</h1>
                <p class="text-xs text-slate-500 mt-0.5">Real-time summary of sales, inventory levels, cash flow, and customer credit.</p>
            </div>

            <!-- Primary Quick Actions -->
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all hover:scale-105 active:scale-95">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span>Start POS Sale</span>
                </a>
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition-all hover:scale-105 active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Add Product</span>
                </a>
                <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-all hover:scale-105 active:scale-95">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                    <span>Receive Stock</span>
                </a>
                <a href="{{ route('payments.customer.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-all hover:scale-105 active:scale-95">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <span>Collect Debt</span>
                </a>
            </div>
        </div>

        <!-- 4 Key Metric Financial & Inventory Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- 1. Today's Revenue -->
            <div class="glass-card rounded-3xl p-5 border border-slate-200/80 shadow-xs bg-white hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Today's Sales</span>
                    <div class="w-9 h-9 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="badge-dollar-sign" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">${{ number_format($todaySales, 2) }}</span>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                        <span>{{ $todaySalesCount }} {{ Str::plural('sale', $todaySalesCount) }} made</span>
                        <span class="font-bold text-emerald-600">${{ number_format($todayCashCollected, 2) }} cash</span>
                    </div>
                </div>
            </div>

            <!-- 2. Inventory Valuation -->
            <div class="glass-card rounded-3xl p-5 border border-slate-200/80 shadow-xs bg-white hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Stock Valuation</span>
                    <div class="w-9 h-9 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i data-lucide="boxes" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">${{ number_format($valuationAtRetail, 2) }}</span>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                        <span>{{ $totalProductsCount }} items cataloged</span>
                        <span class="font-bold text-slate-700">{{ $totalStockUnits }} in stock</span>
                    </div>
                </div>
            </div>

            <!-- 3. Customer Debt to Collect -->
            <div class="glass-card rounded-3xl p-5 border border-slate-200/80 shadow-xs bg-white hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Customer Credit</span>
                    <div class="w-9 h-9 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-amber-600">${{ number_format($totalCustomerReceivables, 2) }}</span>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                        <span>Unpaid balances</span>
                        <a href="{{ route('payments.index') }}" class="font-bold text-indigo-600 hover:underline">Collect &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- 4. Stock Health Alert Status -->
            <div class="glass-card rounded-3xl p-5 border border-slate-200/80 shadow-xs bg-white hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Stock Health</span>
                    <div class="w-9 h-9 rounded-2xl {{ ($lowStockCount + $outOfStockCount) > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center">
                        <i data-lucide="{{ ($lowStockCount + $outOfStockCount) > 0 ? 'alert-triangle' : 'check' }}" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    @if(($lowStockCount + $outOfStockCount) > 0)
                        <span class="text-2xl sm:text-3xl font-black text-rose-600">{{ $lowStockCount + $outOfStockCount }} Items</span>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                            <span>{{ $outOfStockCount }} empty &bull; {{ $lowStockCount }} low</span>
                            <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" class="font-bold text-rose-600 hover:underline">Restock &rarr;</a>
                        </div>
                    @else
                        <span class="text-2xl sm:text-3xl font-black text-emerald-600">All Good</span>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-100">
                            <span>No stock shortages</span>
                            <span class="font-bold text-emerald-600">Healthy</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 7-Day Revenue Trend Visual Bar -->
        <div class="glass-card rounded-3xl p-6 border border-slate-200/80 shadow-xs bg-white">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">7-Day Sales Performance</h2>
                    <p class="text-xs text-slate-500">Daily sales revenue trends</p>
                </div>
                <span class="text-xs font-semibold text-slate-500">Past 7 Days Total: <strong class="text-slate-900">${{ number_format(collect($salesTrend)->sum('total'), 2) }}</strong></span>
            </div>

            @php
                $maxTrend = max(1, collect($salesTrend)->max('total'));
            @endphp
            <div class="grid grid-cols-7 gap-2 sm:gap-4 items-end h-36 pt-4">
                @foreach($salesTrend as $day)
                    @php
                        $percentage = min(100, max(8, ($day['total'] / $maxTrend) * 100));
                        $isToday = $loop->last;
                    @endphp
                    <div class="flex flex-col items-center h-full justify-end group">
                        <div class="text-[11px] font-bold text-slate-700 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            ${{ number_format($day['total'], 0) }}
                        </div>
                        <div class="w-full max-w-[48px] rounded-xl transition-all duration-300 {{ $isToday ? 'bg-gradient-to-t from-emerald-600 to-teal-500 shadow-md shadow-emerald-500/20' : 'bg-slate-200 hover:bg-slate-300' }}" style="height: {{ $percentage }}%;"></div>
                        <span class="text-[11px] font-bold mt-2 {{ $isToday ? 'text-emerald-700 font-extrabold' : 'text-slate-500' }}">{{ $day['day'] }}</span>
                        <span class="text-[9px] text-slate-400">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main 2-Column Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left: Today's Recent Sales Invoices (7 Cols) -->
            <div class="lg:col-span-7 glass-card rounded-3xl p-6 border border-slate-200/80 shadow-xs bg-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i data-lucide="receipt" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Latest Sales Invoices</h3>
                                <p class="text-[11px] text-slate-400">Recent customer transactions</p>
                            </div>
                        </div>
                        <a href="{{ route('sales.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                            <span>View All</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>

                    @if($recentSales->isEmpty())
                        <div class="py-12 text-center text-slate-400 text-xs">
                            <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                            <p class="font-medium text-slate-600">No sales recorded yet today.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($recentSales as $sale)
                                <div class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/50 rounded-xl px-2 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                                            #
                                        </div>
                                        <div>
                                            <a href="{{ route('sales.show', $sale) }}" class="text-xs font-bold text-slate-900 hover:text-emerald-600 block">
                                                {{ $sale->invoice_number }}
                                            </a>
                                            <span class="text-[11px] text-slate-400">
                                                {{ $sale->customer->name ?? 'Walk-In Customer' }} &bull; {{ $sale->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-xs font-black text-slate-900 block">${{ number_format($sale->grand_total, 2) }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sale->payment_status === 'PAID' ? 'bg-emerald-50 text-emerald-700' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                            {{ $sale->payment_status === 'PAID' ? 'PAID' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'PARTIAL' : 'CREDIT') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 mt-3 text-center">
                    <a href="{{ route('sales.create') }}" class="text-xs font-bold text-emerald-600 hover:underline">
                        + New Sale Transaction
                    </a>
                </div>
            </div>

            <!-- Right: Low Stock Watchlist (5 Cols) -->
            <div class="lg:col-span-5 glass-card rounded-3xl p-6 border border-slate-200/80 shadow-xs bg-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Low Stock Watchlist</h3>
                                <p class="text-[11px] text-slate-400">Items running out</p>
                            </div>
                        </div>
                        <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" class="text-xs font-bold text-amber-600 hover:text-amber-700 flex items-center gap-1">
                            <span>Manage</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>

                    @if($lowStockProducts->isEmpty())
                        <div class="py-12 text-center text-slate-400 text-xs">
                            <i data-lucide="check-circle-2" class="w-8 h-8 mx-auto text-emerald-400 mb-2"></i>
                            <p class="font-semibold text-slate-700">All products are adequately stocked.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($lowStockProducts as $prod)
                                <div class="py-2.5 flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-900 truncate">{{ $prod->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono">{{ $prod->sku }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-black {{ $prod->current_stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                            {{ $prod->current_stock }} {{ $prod->unit }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400">Min: {{ $prod->minimum_stock_level }}</span>
                                    </div>
                                    <a href="{{ route('purchases.create', ['product_id' => $prod->id]) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold shrink-0">
                                        + Stock
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 mt-3 text-center">
                    <a href="{{ route('products.create') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">
                        + Add New Product to Store
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
