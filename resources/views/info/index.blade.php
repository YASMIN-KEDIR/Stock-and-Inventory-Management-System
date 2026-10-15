<x-layouts.app>
    <x-slot:title>Stock & Sales Information</x-slot:title>
    <x-slot:headerTitle>Stock & Sales Info</x-slot:headerTitle>
    <x-slot:headerSubtitle>
        @if($tab === 'stock')
            Stock Information & Visual Charts
        @elseif($tab === 'invoices')
            Sales Invoices History
        @else
            Stock Value & Pricing Totals
        @endif
    </x-slot:headerSubtitle>

    <div class="space-y-6">

        <!-- Top Header & Tabs (Simple English, No Abbreviations) -->
        <div class="glass-card rounded-3xl p-5 border border-slate-200/80 bg-white shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-800 border border-amber-300">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600"></i>
                        <span>Info Section &bull; Stock & Sales Information</span>
                    </span>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1.5">
                        @if($tab === 'stock')
                            Stock Information & Charts
                        @elseif($tab === 'invoices')
                            Sales Invoices History
                        @else
                            Stock Value & Profit Margins
                        @endif
                    </h1>
                    <p class="text-xs text-slate-600 font-semibold mt-0.5">
                        @if($tab === 'stock')
                            Visual pie charts, stock health graphs, and complete movement history.
                        @elseif($tab === 'invoices')
                            All customer sales invoices, cash payments received, and unpaid balances.
                        @else
                            Total money invested in buying stock versus total sales income.
                        @endif
                    </p>
                </div>

                <!-- Three Direct Switcher Tabs -->
                <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shrink-0 overflow-x-auto">
                    <a href="{{ route('info.index', ['tab' => 'stock']) }}" 
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black transition-all whitespace-nowrap {{ $tab === 'stock' ? 'bg-white text-cyan-700 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900' }}">
                        <i data-lucide="pie-chart" class="w-4 h-4 text-cyan-600"></i>
                        <span>Stock Information (Charts)</span>
                    </a>

                    <a href="{{ route('info.index', ['tab' => 'valuation']) }}" 
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black transition-all whitespace-nowrap {{ $tab === 'valuation' ? 'bg-white text-amber-700 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900' }}">
                        <i data-lucide="coins" class="w-4 h-4 text-amber-600"></i>
                        <span>Stock Value & Prices</span>
                    </a>

                    <a href="{{ route('info.index', ['tab' => 'invoices']) }}" 
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black transition-all whitespace-nowrap {{ $tab === 'invoices' ? 'bg-white text-emerald-700 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900' }}">
                        <i data-lucide="receipt" class="w-4 h-4 text-emerald-600"></i>
                        <span>Sales Invoices</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 4 Core Valuation Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Buying Price -->
            <div class="glass-card rounded-2xl p-5 border-2 border-indigo-200 bg-gradient-to-br from-indigo-50/50 to-white shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-indigo-900">Total Buying Price (All)</span>
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalBuyingPrice, 2) }} <span class="text-xs font-bold text-slate-500">Birr</span></p>
                <p class="text-xs font-bold text-slate-600 mt-1">Total money spent to buy current stock</p>
            </div>

            <!-- 2. Total Selling Price -->
            <div class="glass-card rounded-2xl p-5 border-2 border-emerald-200 bg-gradient-to-br from-emerald-50/50 to-white shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-emerald-900">Total Selling Price (All)</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i data-lucide="tag" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-2xl font-black text-emerald-700 mt-2">{{ number_format($totalSellingPrice, 2) }} <span class="text-xs font-bold text-slate-500">Birr</span></p>
                <p class="text-xs font-bold text-slate-600 mt-1">Expected money when current stock is sold</p>
            </div>

            <!-- 3. Projected Profit -->
            <div class="glass-card rounded-2xl p-5 border-2 border-amber-200 bg-gradient-to-br from-amber-50/50 to-white shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-amber-900">Expected Profit</span>
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-2xl font-black text-amber-700 mt-2">{{ number_format($totalProjectedProfit, 2) }} <span class="text-xs font-bold text-slate-500">Birr</span></p>
                <p class="text-xs font-bold text-slate-600 mt-1">
                    Profit Margin: <span class="font-black text-slate-900">{{ number_format($projectedMarginPercent, 1) }}%</span>
                </p>
            </div>

            <!-- 4. Total Stock Units -->
            <div class="glass-card rounded-2xl p-5 border-2 border-slate-200 bg-white shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700">Total Items in Stock</span>
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                        <i data-lucide="boxes" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-2xl font-black text-slate-900 mt-2">{{ number_format($totalStockUnits) }}</p>
                <p class="text-xs font-bold text-slate-600 mt-1">
                    Across <span class="text-slate-900 font-black">{{ $totalProductsCount }}</span> active products
                </p>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 1: STOCK INFORMATION (VISUAL PIE CHART & BAR GRAPH + MOVEMENT LOG)    --}}
        {{-- ========================================================================= --}}
        @if($tab === 'stock')

            <!-- VISUAL CHARTS SECTION (Pie Chart + Bar Graph) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Pie Chart: Stock Health Status -->
                <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                    <i data-lucide="pie-chart" class="w-4 h-4"></i>
                                </div>
                                <h3 class="text-sm font-black text-slate-900">Stock Status Pie Chart</h3>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500">{{ $totalProductsCount }} Products</span>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold mt-2">Visual breakdown of items currently in stock, running low, or completely out.</p>
                    </div>

                    <!-- Chart Canvas -->
                    <div class="relative h-56 my-3 flex items-center justify-center">
                        <canvas id="stockStatusPieChart"></canvas>
                    </div>

                    <!-- Direct Numbers Legend -->
                    <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 text-center">
                        <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-200">
                            <span class="text-[10px] font-bold text-emerald-800 uppercase block">In Stock</span>
                            <span class="text-base font-black text-emerald-700">{{ $inStockCount }}</span>
                        </div>
                        <div class="p-2 rounded-xl bg-amber-50 border border-amber-200">
                            <span class="text-[10px] font-bold text-amber-800 uppercase block">Low Stock</span>
                            <span class="text-base font-black text-amber-700">{{ $lowStockCount }}</span>
                        </div>
                        <div class="p-2 rounded-xl bg-rose-50 border border-rose-200">
                            <span class="text-[10px] font-bold text-rose-800 uppercase block">Out of Stock</span>
                            <span class="text-base font-black text-rose-700">{{ $outOfStockCount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Bar Graph: Top Products in Stock -->
                <div class="lg:col-span-2 glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                                </div>
                                <h3 class="text-sm font-black text-slate-900">Top Products by Stock Quantity</h3>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500">Highest Available Units</span>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold mt-2">Compare which products have the largest quantity of inventory ready for sale.</p>
                    </div>

                    <!-- Bar Chart Canvas -->
                    <div class="relative h-64 my-3">
                        <canvas id="topProductsBarChart"></canvas>
                    </div>

                    <!-- Quick Stock Flow Stats -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">+</div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">Purchased In</span>
                                <span class="text-xs font-black text-slate-900">+{{ number_format($totalPurchasesIn) }} items</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-xs shrink-0">-</div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">Sold Out</span>
                                <span class="text-xs font-black text-slate-900">-{{ number_format($totalSalesOut) }} items</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs shrink-0">~</div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">Adjustments</span>
                                <span class="text-xs font-black text-slate-900">+{{ $totalAdjustmentsAdd }} / -{{ $totalAdjustmentsSub }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Filter & Manual Adjustment Bar -->
            <div class="glass-card rounded-2xl p-4 border border-slate-200/80 bg-white shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <form method="GET" action="{{ route('info.index') }}" class="flex-1 flex flex-wrap items-center gap-3">
                    <input type="hidden" name="tab" value="stock">

                    <select name="type" class="px-3.5 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                        <option value="">All Movement Types</option>
                        <option value="PURCHASE" {{ request('type') === 'PURCHASE' ? 'selected' : '' }}>Stock In (Purchases)</option>
                        <option value="SALE" {{ request('type') === 'SALE' ? 'selected' : '' }}>Stock Out (Sales)</option>
                        <option value="ADJUSTMENT_ADD" {{ request('type') === 'ADJUSTMENT_ADD' ? 'selected' : '' }}>Manual Stock Add (+)</option>
                        <option value="ADJUSTMENT_SUB" {{ request('type') === 'ADJUSTMENT_SUB' ? 'selected' : '' }}>Manual Stock Remove (-)</option>
                    </select>

                    <select name="product_id" class="px-3.5 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                        <option value="">All Products</option>
                        @foreach($allProducts as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                        Filter Log
                    </button>
                    @if(request()->hasAny(['type', 'product_id']))
                        <a href="{{ route('info.index', ['tab' => 'stock']) }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold" title="Reset Filters">
                            ✕
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('stock-adjustments.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-xs transition-all">
                        <i data-lucide="sliders" class="w-4 h-4"></i>
                        <span>Manual Stock Adjustment</span>
                    </a>
                </div>
            </div>

            <!-- Stock Movement History Table -->
            <div class="glass-card rounded-3xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Stock Movement History</h2>
                        <p class="text-xs text-slate-500 font-semibold">Every stock inflow, customer sale deduction, and quantity adjustment.</p>
                    </div>
                    <span class="text-xs font-black px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $stockTransactions->total() }} Records
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 uppercase tracking-wider font-extrabold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Date & Time</th>
                                <th class="px-4 py-3.5">Product Name</th>
                                <th class="px-4 py-3.5">Movement Type</th>
                                <th class="px-4 py-3.5 text-center">Quantity Changed</th>
                                <th class="px-4 py-3.5 text-center">Old Stock</th>
                                <th class="px-4 py-3.5 text-center">New Stock</th>
                                <th class="px-4 py-3.5">Reason</th>
                                <th class="px-4 py-3.5 text-right">Staff Member</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80">
                            @forelse($stockTransactions as $tx)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-mono text-[11px] text-slate-600 font-bold whitespace-nowrap">
                                        {{ $tx->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('products.show', $tx->product_id) }}" class="font-bold text-slate-900 hover:text-indigo-600">
                                            {{ $tx->product->name ?? 'Product Deleted' }}
                                        </a>
                                        <span class="block font-mono text-[10px] text-slate-500 font-semibold">Code: {{ $tx->product->sku ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($tx->type === 'PURCHASE')
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">Purchased In</span>
                                        @elseif($tx->type === 'SALE')
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black bg-sky-100 text-sky-800 border border-sky-300">Customer Sale</span>
                                        @elseif($tx->type === 'ADJUSTMENT_ADD')
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-100 text-indigo-800 border border-indigo-300">Added (+)</span>
                                        @elseif($tx->type === 'ADJUSTMENT_SUB')
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">Removed (-)</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-black bg-slate-100 text-slate-800">{{ $tx->type }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-black text-sm {{ $tx->quantity > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                        {{ $tx->quantity > 0 ? '+' . $tx->quantity : $tx->quantity }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-600">
                                        {{ $tx->balance_before }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-extrabold text-slate-900">
                                        {{ $tx->balance_after }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-700 font-semibold">
                                        {{ $tx->reason ?? ($tx->reference_type ? $tx->reference_type . ' #' . $tx->reference_id : '—') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-600 font-bold">
                                        {{ $tx->user->name ?? 'System' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-slate-500 font-bold">
                                        No stock movements recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($stockTransactions->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $stockTransactions->links() }}
                    </div>
                @endif
            </div>

            <!-- Client Script for Interactive Charts -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // 1. Stock Status Pie/Doughnut Chart
                    const pieEl = document.getElementById('stockStatusPieChart');
                    if (pieEl && typeof Chart !== 'undefined') {
                        new Chart(pieEl, {
                            type: 'doughnut',
                            data: {
                                labels: ['In Stock (Good)', 'Low Stock (Reorder)', 'Out of Stock (Empty)'],
                                datasets: [{
                                    data: [{{ $inStockCount }}, {{ $lowStockCount }}, {{ $outOfStockCount }}],
                                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                                    borderColor: '#ffffff',
                                    borderWidth: 3,
                                    hoverOffset: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            boxWidth: 12,
                                            padding: 10,
                                            font: { size: 11, weight: 'bold' }
                                        }
                                    }
                                },
                                cutout: '65%'
                            }
                        });
                    }

                    // 2. Top Products Bar Chart
                    const barEl = document.getElementById('topProductsBarChart');
                    if (barEl && typeof Chart !== 'undefined') {
                        new Chart(barEl, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode($topStockProducts->pluck('name')) !!},
                                datasets: [{
                                    label: 'Units in Stock',
                                    data: {!! json_encode($topStockProducts->pluck('current_stock')) !!},
                                    backgroundColor: '#6366f1',
                                    borderRadius: 8,
                                    maxBarThickness: 36
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { font: { weight: 'bold' } },
                                        grid: { color: '#f1f5f9' }
                                    },
                                    x: {
                                        ticks: { font: { weight: 'bold', size: 10 } },
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                    }
                });
            </script>

        {{-- ========================================================================= --}}
        {{-- TAB 2: STOCK VALUE & PRICING TOTALS                                       --}}
        {{-- ========================================================================= --}}
        @elseif($tab === 'valuation')
            <!-- Filter & Search Controls -->
            <div class="glass-card rounded-2xl p-4 border border-slate-200/80 bg-white shadow-xs">
                <form method="GET" action="{{ route('info.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <input type="hidden" name="tab" value="valuation">
                    
                    <!-- Search -->
                    <div class="lg:col-span-2">
                        <div class="relative">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search product name or code..." 
                                   class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <select name="category_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stock Status Filter -->
                    <div>
                        <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                            <option value="">All Stock Statuses</option>
                            <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>🟢 In Stock</option>
                            <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>🟠 Low Stock ({{ $lowStockCount }})</option>
                            <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>🔴 Out of Stock ({{ $outOfStockCount }})</option>
                        </select>
                    </div>

                    <!-- Sort & Submit -->
                    <div class="flex items-center gap-2">
                        <select name="sort" class="flex-1 px-3 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Alphabetical (A-Z)</option>
                            <option value="buying_val_desc" {{ request('sort') === 'buying_val_desc' ? 'selected' : '' }}>Highest Buying Cost</option>
                            <option value="selling_val_desc" {{ request('sort') === 'selling_val_desc' ? 'selected' : '' }}>Highest Selling Value</option>
                            <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Highest Stock</option>
                            <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Lowest Stock</option>
                        </select>

                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'category_id', 'status', 'sort']))
                            <a href="{{ route('info.index', ['tab' => 'valuation']) }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold" title="Reset Filters">
                                ✕
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Master Product Buying & Selling Price Table -->
            <div class="glass-card rounded-3xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Product Buying vs Selling Price Breakdown</h2>
                        <p class="text-xs text-slate-500 font-semibold">Every product with unit cost, selling price, total stock value, and profit.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                            Showing {{ $products->total() }} Products
                        </span>
                        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Add Product</span>
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 uppercase tracking-wider font-extrabold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Product Name</th>
                                <th class="px-4 py-3.5">Category</th>
                                <th class="px-4 py-3.5 text-center">In Stock</th>
                                <th class="px-4 py-3.5 text-right bg-indigo-50/50 text-indigo-900">Unit Cost</th>
                                <th class="px-4 py-3.5 text-right bg-indigo-100/40 text-indigo-950 font-black">Total Cost Value</th>
                                <th class="px-4 py-3.5 text-right bg-emerald-50/50 text-emerald-900">Unit Sell Price</th>
                                <th class="px-4 py-3.5 text-right bg-emerald-100/40 text-emerald-950 font-black">Total Selling Value</th>
                                <th class="px-4 py-3.5 text-right bg-amber-50/50 text-amber-950 font-black">Expected Profit</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80">
                            @forelse($products as $product)
                                @php
                                    $rowBuyingTotal = $product->current_stock * $product->cost_price;
                                    $rowSellingTotal = $product->current_stock * $product->selling_price;
                                    $rowProfit = $rowSellingTotal - $rowBuyingTotal;
                                    $rowMargin = $rowSellingTotal > 0 ? (($rowProfit / $rowSellingTotal) * 100) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('products.show', $product) }}" class="font-extrabold text-slate-900 hover:text-indigo-600 block">
                                            {{ $product->name }}
                                        </a>
                                        <span class="font-mono text-[11px] text-slate-600 font-bold">Code: {{ $product->sku }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-700">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-black text-sm {{ $product->current_stock <= 0 ? 'text-rose-600' : ($product->current_stock <= $product->minimum_stock_level ? 'text-amber-600' : 'text-slate-900') }}">
                                        {{ number_format($product->current_stock) }} <span class="text-[10px] text-slate-500 font-bold">{{ $product->unit }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800 bg-indigo-50/30">
                                        {{ number_format($product->cost_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-indigo-900 bg-indigo-100/30">
                                        {{ number_format($rowBuyingTotal, 2) }} <span class="text-[10px] text-slate-500 font-normal">Birr</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800 bg-emerald-50/30">
                                        {{ number_format($product->selling_price, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-emerald-800 bg-emerald-100/30">
                                        {{ number_format($rowSellingTotal, 2) }} <span class="text-[10px] text-slate-500 font-normal">Birr</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-black bg-amber-50/30 {{ $rowProfit >= 0 ? 'text-amber-800' : 'text-rose-700' }}">
                                        <div>{{ number_format($rowProfit, 2) }} <span class="text-[10px] text-slate-500 font-normal">Birr</span></div>
                                        <div class="text-[10px] font-bold text-slate-600">({{ number_format($rowMargin, 1) }}%)</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($product->current_stock <= 0)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">Out of Stock</span>
                                        @elseif($product->current_stock <= $product->minimum_stock_level)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">Low Stock</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">In Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-10 text-center text-slate-500 font-bold">
                                        No products found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB 3: SALES INVOICES HISTORY                                             --}}
        {{-- ========================================================================= --}}
        @elseif($tab === 'invoices')
            <!-- Invoices Summary Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="glass-card rounded-2xl p-4 border border-slate-200 bg-white">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Invoices Created</span>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalSalesCount) }}</p>
                </div>
                <div class="glass-card rounded-2xl p-4 border border-emerald-200 bg-emerald-50/30">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Total Sales Money</span>
                    <p class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($totalSalesRevenue, 2) }} <span class="text-xs font-bold text-slate-500">Birr</span></p>
                </div>
                <div class="glass-card rounded-2xl p-4 border border-amber-200 bg-amber-50/30">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Unpaid Customer Credit</span>
                    <p class="text-2xl font-black text-amber-700 mt-1">{{ number_format($totalSalesDebt, 2) }} <span class="text-xs font-bold text-slate-500">Birr</span></p>
                </div>
            </div>

            <!-- Invoices Search & Filter Controls -->
            <div class="glass-card rounded-2xl p-4 border border-slate-200/80 bg-white shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <form method="GET" action="{{ route('info.index') }}" class="flex-1 flex flex-wrap items-center gap-3">
                    <input type="hidden" name="tab" value="invoices">

                    <div class="relative flex-1 min-w-[200px]">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice number or customer name..." 
                               class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                    </div>

                    <select name="payment_status" class="px-3.5 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                        <option value="">All Payment Statuses</option>
                        <option value="PAID" {{ request('payment_status') === 'PAID' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="PARTIAL" {{ request('payment_status') === 'PARTIAL' ? 'selected' : '' }}>Partial Paid</option>
                        <option value="UNPAID" {{ request('payment_status') === 'UNPAID' ? 'selected' : '' }}>Unpaid Credit</option>
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                        Search
                    </button>
                    @if(request()->hasAny(['search', 'payment_status']))
                        <a href="{{ route('info.index', ['tab' => 'invoices']) }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold" title="Reset Filters">
                            ✕
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-xs transition-all">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        <span>+ New Sale</span>
                    </a>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="glass-card rounded-3xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Sales Invoices History</h2>
                        <p class="text-xs text-slate-500 font-semibold">Every invoice created with bill total, amount received, and remaining credit debt.</p>
                    </div>
                    <span class="text-xs font-black px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $sales->total() }} Invoices
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 uppercase tracking-wider font-extrabold border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3.5">Invoice Number</th>
                                <th class="px-4 py-3.5">Customer Name</th>
                                <th class="px-4 py-3.5">Date</th>
                                <th class="px-4 py-3.5 text-right">Total Bill (Birr)</th>
                                <th class="px-4 py-3.5 text-right">Cash Paid</th>
                                <th class="px-4 py-3.5 text-right">Remaining Debt</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <th class="px-4 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80">
                            @forelse($sales as $sale)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:underline">
                                            {{ $sale->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 font-bold text-slate-800">
                                        {{ $sale->customer->name ?? 'Walk-In Customer (Cash)' }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-600 whitespace-nowrap">
                                        {{ $sale->sale_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-slate-900">
                                        {{ number_format($sale->grand_total, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-emerald-700">
                                        {{ number_format($sale->amount_paid, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black {{ $sale->remaining_balance > 0 ? 'text-amber-700' : 'text-slate-400' }}">
                                        {{ number_format($sale->remaining_balance, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($sale->payment_status === 'PAID')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">Paid in Full</span>
                                        @elseif($sale->payment_status === 'PARTIAL')
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 border border-amber-300">Partial Paid</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-800 border border-rose-300">Credit / Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('sales.show', $sale) }}" class="p-1 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-100" title="View Details">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('sales.print', $sale) }}" target="_blank" class="p-1 rounded-lg text-slate-600 hover:text-emerald-600 hover:bg-slate-100" title="Print Invoice">
                                                <i data-lucide="printer" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-slate-500 font-bold">
                                        No sales invoices found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sales->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-layouts.app>
