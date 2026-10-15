<x-layouts.app>
    <x-slot:title>Dashboard</x-slot:title>
    <x-slot:headerTitle>Dashboard</x-slot:headerTitle>

    <div class="space-y-6 max-w-7xl mx-auto">

        <!-- Search Station -->
        <div class="glass-card rounded-3xl p-5 sm:p-6 border-2 border-indigo-200 bg-white shadow-xs" 
             x-data="{ searchTab: '{{ !empty($searchDate) ? 'date' : ($searchMode === 'customer' ? 'customer' : ($searchMode === 'product' ? 'product' : 'any')) }}' }">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-slate-200/80">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-black text-xs">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <h2 class="text-base font-black text-slate-900">Search</h2>
                </div>

                <!-- 3 Quick Search Switcher Tabs -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs">
                    <button type="button" @click="searchTab = 'any'" :class="searchTab === 'any' ? 'bg-white font-black text-indigo-700 shadow-xs' : 'font-bold text-slate-600'" class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                        🔍 All / Quick
                    </button>
                    <button type="button" @click="searchTab = 'customer'" :class="searchTab === 'customer' ? 'bg-white font-black text-indigo-700 shadow-xs' : 'font-bold text-slate-600'" class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                        👥 Customer Name
                    </button>
                    <button type="button" @click="searchTab = 'product'" :class="searchTab === 'product' ? 'bg-white font-black text-indigo-700 shadow-xs' : 'font-bold text-slate-600'" class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                        📦 Product Name
                    </button>
                    <button type="button" @click="searchTab = 'date'" :class="searchTab === 'date' ? 'bg-white font-black text-indigo-700 shadow-xs' : 'font-bold text-slate-600'" class="px-3 py-1.5 rounded-lg transition-colors cursor-pointer">
                        📅 By Date
                    </button>
                </div>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('dashboard') }}" class="pt-4">
                <input type="hidden" name="search_mode" :value="searchTab === 'customer' ? 'customer' : (searchTab === 'product' ? 'product' : 'all')">
                
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    
                    <!-- Search Query Input -->
                    <div class="md:col-span-7" x-show="searchTab !== 'date'">
                        <div class="relative">
                            <i data-lucide="search" class="w-5 h-5 text-indigo-500 absolute left-3.5 top-3.5"></i>
                            <input type="text" name="q" value="{{ $searchQuery }}" 
                                   :placeholder="searchTab === 'customer' ? 'Enter Customer name or phone number...' : (searchTab === 'product' ? 'Enter Product name, SKU, or barcode...' : 'Search by Customer name, Product name, or SKU...')"
                                   class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        </div>
                    </div>

                    <!-- Date Search Input -->
                    <div :class="searchTab === 'date' ? 'md:col-span-10' : 'md:col-span-3'">
                        <div class="relative">
                            <i data-lucide="calendar" class="w-5 h-5 text-purple-500 absolute left-3.5 top-3.5"></i>
                            <input type="date" name="search_date" value="{{ $searchDate }}" 
                                   class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        </div>
                    </div>

                    <!-- Search Submit & Reset Buttons -->
                    <div :class="searchTab === 'date' ? 'md:col-span-2' : 'md:col-span-2'" class="flex items-center gap-2">
                        <button type="submit" class="w-full py-3 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md transition-all cursor-pointer flex items-center justify-center gap-2">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            <span>Search</span>
                        </button>
                        @if($hasSearch)
                            <a href="{{ route('dashboard') }}" class="p-3 rounded-2xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-black text-xs shrink-0" title="Clear Search">
                                ✕
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- SEARCH RESULTS DISPLAY SECTION (WHEN SEARCH IS ACTIVE) -->
        @if($hasSearch)
            <div class="space-y-6">

                <!-- 1. DATE SEARCH RESULTS -->
                @if($dateSummary)
                    <div class="glass-card rounded-3xl p-6 border-2 border-purple-300 bg-white shadow-md">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider text-purple-700 bg-purple-50 px-2.5 py-1 rounded-full border border-purple-200">
                                    📅 Financial Summary for {{ $dateSummary['date'] }}
                                </span>
                                <h3 class="text-xl font-black text-slate-900 mt-2">Day Financial Breakdown</h3>
                            </div>
                            <span class="text-xs font-bold text-slate-500">{{ $dateSummary['invoices_count'] }} Invoices issued</span>
                        </div>

                        <!-- Date Payment Channel Breakdown Cards -->
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 my-4">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] font-black uppercase text-slate-500 block">Total Sales</span>
                                <p class="text-lg font-black text-slate-900 mt-1">{{ number_format($dateSummary['total_sales'], 2) }} ETB</p>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
                                <span class="text-[10px] font-black uppercase text-emerald-800 block">💵 In Cash</span>
                                <p class="text-lg font-black text-emerald-700 mt-1">{{ number_format($dateSummary['cash'], 2) }} ETB</p>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-cyan-50 border border-cyan-200">
                                <span class="text-[10px] font-black uppercase text-cyan-800 block">📱 In Telebirr</span>
                                <p class="text-lg font-black text-cyan-700 mt-1">{{ number_format($dateSummary['telebirr'], 2) }} ETB</p>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-200">
                                <span class="text-[10px] font-black uppercase text-blue-800 block">🏦 In CBE</span>
                                <p class="text-lg font-black text-blue-700 mt-1">{{ number_format($dateSummary['cbe'], 2) }} ETB</p>
                            </div>
                            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200">
                                <span class="text-[10px] font-black uppercase text-amber-800 block">⏳ In Credit (ብድር)</span>
                                <p class="text-lg font-black text-amber-700 mt-1">{{ number_format($dateSummary['credit_issued'], 2) }} ETB</p>
                            </div>
                        </div>

                        <!-- Invoices List on this date -->
                        <div class="overflow-x-auto mt-4">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-100 text-slate-700 text-[10px] uppercase font-black">
                                    <tr>
                                        <th class="px-4 py-2.5">Invoice #</th>
                                        <th class="px-4 py-2.5">Customer</th>
                                        <th class="px-4 py-2.5 text-right">Total</th>
                                        <th class="px-4 py-2.5 text-right">Paid</th>
                                        <th class="px-4 py-2.5 text-right">Credit (Due)</th>
                                        <th class="px-4 py-2.5 text-center">Status</th>
                                        <th class="px-4 py-2.5 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($dateSalesResults as $ds)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-2.5 font-mono font-bold">{{ $ds->invoice_number }}</td>
                                            <td class="px-4 py-2.5 font-bold">{{ $ds->customer->name ?? 'Walk-In Customer' }}</td>
                                            <td class="px-4 py-2.5 text-right font-black">{{ number_format($ds->grand_total, 2) }} ETB</td>
                                            <td class="px-4 py-2.5 text-right font-bold text-emerald-700">{{ number_format($ds->amount_paid, 2) }} ETB</td>
                                            <td class="px-4 py-2.5 text-right font-bold text-amber-700">{{ number_format($ds->remaining_balance, 2) }} ETB</td>
                                            <td class="px-4 py-2.5 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $ds->payment_status === 'PAID' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                                    {{ $ds->payment_status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2.5 text-center">
                                                <a href="{{ route('sales.show', $ds) }}" class="text-indigo-600 font-bold hover:underline">View &rarr;</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="py-6 text-center text-slate-400">No invoices on this date.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- 2. CUSTOMER SEARCH RESULTS (FULL CUSTOMER INFO, DEBT & INVOICES) -->
                @if($customerSearchResults->count() > 0)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs">👥</span>
                                <span>Customer Info Results ({{ $customerSearchResults->count() }} Found)</span>
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($customerSearchResults as $cust)
                                @php
                                    $custTotalPurchases = (float) $cust->sales->sum('grand_total');
                                    $custTotalPaid = (float) $cust->payments->sum('amount');
                                    $custTotalDebt = (float) $cust->sales->sum('remaining_balance');
                                @endphp
                                <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs space-y-4">
                                    
                                    <!-- Header & Contact -->
                                    <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                                        <div>
                                            <h4 class="text-lg font-black text-slate-900">{{ $cust->name }}</h4>
                                            <div class="flex items-center gap-3 text-xs text-slate-500 font-semibold mt-1">
                                                @if($cust->phone)
                                                    <span>📞 {{ $cust->phone }}</span>
                                                @endif
                                                @if($cust->email)
                                                    <span>✉️ {{ $cust->email }}</span>
                                                @endif
                                            </div>
                                            @if($cust->address)
                                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">📍 {{ $cust->address }}</p>
                                            @endif
                                        </div>
                                        
                                        <!-- Outstanding Debt Pill -->
                                        <div class="text-right">
                                            <span class="text-[10px] font-black uppercase text-slate-400 block">Total Credit Debt (ብድር)</span>
                                            <span class="text-xl font-black {{ $custTotalDebt > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                                {{ number_format($custTotalDebt, 2) }} ETB
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Quick Financial Stats -->
                                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-[10px] text-slate-400 font-bold block">Invoices</span>
                                            <span class="font-black text-slate-900">{{ $cust->sales->count() }} Orders</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-[10px] text-slate-400 font-bold block">Total Purchases</span>
                                            <span class="font-black text-slate-900">{{ number_format($custTotalPurchases, 0) }} ETB</span>
                                        </div>
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-[10px] text-slate-400 font-bold block">Total Paid</span>
                                            <span class="font-black text-emerald-700">{{ number_format($custTotalPaid, 0) }} ETB</span>
                                        </div>
                                    </div>

                                    <!-- Recent Invoices for this customer -->
                                    <div>
                                        <span class="text-xs font-black text-slate-700 block mb-2">Recent Invoices:</span>
                                        <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                            @forelse($cust->sales as $cs)
                                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                                    <div>
                                                        <span class="font-mono font-bold text-slate-900">{{ $cs->invoice_number }}</span>
                                                        <span class="text-[10px] text-slate-400 block">{{ $cs->sale_date->format('M d, Y') }}</span>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="font-black text-slate-900">{{ number_format($cs->grand_total, 2) }} ETB</span>
                                                        <span class="block text-[10px] font-bold {{ $cs->remaining_balance > 0 ? 'text-amber-700' : 'text-emerald-700' }}">
                                                            Due: {{ number_format($cs->remaining_balance, 2) }} ETB
                                                        </span>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-xs text-slate-400">No purchases logged yet.</p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <!-- Customer Action Links -->
                                    <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                                        <a href="{{ route('customers.show', $cust) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors">
                                            Full Profile & Ledger &rarr;
                                        </a>
                                        <a href="{{ route('sales.create') }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                                            + Sell to Customer
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 3. PRODUCT SEARCH RESULTS (BUYING & SELLING PRICES, STOCK & TOTALS) -->
                @if($productSearchResults->count() > 0)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">📦</span>
                                <span>Product Pricing & Inventory Info ({{ $productSearchResults->count() }} Found)</span>
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($productSearchResults as $prod)
                                @php
                                    $pCost = (float) $prod->cost_price;
                                    $pSell = (float) $prod->selling_price;
                                    $pStock = (int) $prod->current_stock;
                                    $pBuyingTotal = $pCost * $pStock;
                                    $pSellingTotal = $pSell * $pStock;
                                    $pMargin = $pSell - $pCost;
                                @endphp
                                <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs space-y-3">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-mono font-bold text-slate-500">SKU: {{ $prod->sku }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $pStock <= 0 ? 'bg-rose-100 text-rose-800' : ($pStock <= $prod->minimum_stock_level ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                                {{ $pStock <= 0 ? 'Out of Stock' : ($pStock <= $prod->minimum_stock_level ? 'Low Stock' : 'In Stock') }}
                                            </span>
                                        </div>
                                        <h4 class="text-base font-black text-slate-900 mt-1">{{ $prod->name }}</h4>
                                        @if($prod->category)
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $prod->category->name }}</span>
                                        @endif
                                    </div>

                                    <!-- Unit Prices & Stock -->
                                    <div class="grid grid-cols-3 gap-2 text-center text-xs pt-2 border-t border-slate-100">
                                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-[10px] text-slate-400 font-bold block">Buying Cost</span>
                                            <span class="font-black text-slate-800">{{ number_format($pCost, 2) }} ETB</span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-[10px] text-slate-400 font-bold block">Selling Price</span>
                                            <span class="font-black text-emerald-700">{{ number_format($pSell, 2) }} ETB</span>
                                        </div>
                                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-200">
                                            <span class="text-[10px] text-slate-400 font-bold block">In-Stock</span>
                                            <span class="font-black text-slate-900">{{ $pStock }} {{ $prod->unit }}</span>
                                        </div>
                                    </div>

                                    <!-- Total Valuation for this item -->
                                    <div class="p-3 rounded-2xl bg-indigo-50/50 border border-indigo-200 text-xs space-y-1">
                                        <div class="flex justify-between">
                                            <span class="text-slate-600 font-bold">Total Buying Value:</span>
                                            <span class="font-black text-slate-900">{{ number_format($pBuyingTotal, 2) }} ETB</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-emerald-800 font-bold">Total Selling Value:</span>
                                            <span class="font-black text-emerald-800">{{ number_format($pSellingTotal, 2) }} ETB</span>
                                        </div>
                                        <div class="flex justify-between pt-1 border-t border-indigo-100">
                                            <span class="text-indigo-900 font-extrabold">Projected Margin:</span>
                                            <span class="font-black text-indigo-900">+{{ number_format($pSellingTotal - $pBuyingTotal, 2) }} ETB</span>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center justify-end gap-2 pt-1">
                                        <a href="{{ route('products.edit', $prod) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                                            Edit Details
                                        </a>
                                        <a href="{{ route('sales.create') }}" class="px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold transition-colors">
                                            Sell in POS &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- No Results Banner -->
                @if(!$dateSummary && $customerSearchResults->isEmpty() && $productSearchResults->isEmpty())
                    <div class="p-8 rounded-3xl bg-white border-2 border-slate-200 text-center space-y-2">
                        <i data-lucide="help-circle" class="w-10 h-10 text-slate-300 mx-auto"></i>
                        <h4 class="text-base font-bold text-slate-800">No matching records found</h4>
                        <p class="text-xs text-slate-500">Could not find any customer, product, or date matching "{{ $searchQuery ?: $searchDate }}". Try a different name or phone number.</p>
                    </div>
                @endif

            </div>
        @endif

        <!-- OVERALL FINANCIAL CHANNELS BREAKDOWN: TELEBIRR, CASH, CBE & CREDITS -->
        <div>
            <div class="flex items-center justify-between mb-3 px-1">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">
                    Overall Finance & Payment Channels (የሂሳብ ሁኔታ)
                </h2>
                <span class="text-xs font-black text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                    Grand Total: {{ number_format($overallFinance, 2) }} ETB
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- 1. CASH (ጥሬ ገንዘብ) -->
                <div class="glass-card rounded-3xl p-5 border-2 border-emerald-300 bg-gradient-to-br from-emerald-50/60 to-white shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black uppercase tracking-wider text-emerald-900">💵 Cash (በጥሬ ገንዘብ)</span>
                        <div class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                            <i data-lucide="wallet" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($allTimeCash, 2) }}</span>
                        <span class="text-xs font-bold text-slate-500"> ETB</span>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                            <span>Today Cash In:</span>
                            <span class="font-black text-emerald-700">+{{ number_format($todayCash, 2) }} ETB</span>
                        </div>
                    </div>
                </div>

                <!-- 2. TELEBIRR (ቴሌብር / Tele) -->
                <div class="glass-card rounded-3xl p-5 border-2 border-cyan-300 bg-gradient-to-br from-cyan-50/60 to-white shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black uppercase tracking-wider text-cyan-900">📱 Telebirr (ቴሌብር)</span>
                        <div class="w-9 h-9 rounded-2xl bg-cyan-100 text-cyan-800 flex items-center justify-center font-bold">
                            <i data-lucide="smartphone" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-2xl sm:text-3xl font-black text-cyan-800">{{ number_format($allTimeTelebirr, 2) }}</span>
                        <span class="text-xs font-bold text-slate-500"> ETB</span>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                            <span>Today Telebirr In:</span>
                            <span class="font-black text-cyan-700">+{{ number_format($todayTelebirr, 2) }} ETB</span>
                        </div>
                    </div>
                </div>

                <!-- 3. CBE (የኢትዮጵያ ንግድ ባንክ / CBE Birr) -->
                <div class="glass-card rounded-3xl p-5 border-2 border-purple-300 bg-gradient-to-br from-purple-50/60 to-white shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black uppercase tracking-wider text-purple-900">🏦 CBE (ንግድ ባንክ)</span>
                        <div class="w-9 h-9 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center font-bold">
                            <i data-lucide="building" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-2xl sm:text-3xl font-black text-purple-800">{{ number_format($allTimeCbe, 2) }}</span>
                        <span class="text-xs font-bold text-slate-500"> ETB</span>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                            <span>Today CBE In:</span>
                            <span class="font-black text-purple-700">+{{ number_format($todayCbe, 2) }} ETB</span>
                        </div>
                    </div>
                </div>

                <!-- 4. CREDITS (ያልተከፈለ የደንበኛ ብድር) -->
                <div class="glass-card rounded-3xl p-5 border-2 border-amber-300 bg-gradient-to-br from-amber-50/60 to-white shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-black uppercase tracking-wider text-amber-900">⏳ Credits (የደንበኛ ብድር)</span>
                        <div class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-2xl sm:text-3xl font-black text-amber-700">{{ number_format($totalCustomerReceivables, 2) }}</span>
                        <span class="text-xs font-bold text-slate-500"> ETB</span>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                            <span>Today Credit Added:</span>
                            <span class="font-black text-amber-700">+{{ number_format($todayCredits, 2) }} ETB</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <!-- 4 Key Metric Financial & Inventory Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Today's Revenue -->
            <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-600">Today's Total Sales</span>
                    <div class="w-9 h-9 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i data-lucide="badge-dollar-sign" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($todaySales, 2) }}</span>
                    <span class="text-xs font-bold text-slate-500"> ETB</span>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                        <span>{{ $todaySalesCount }} {{ Str::plural('sale', $todaySalesCount) }} made</span>
                        <span class="font-black text-emerald-700">{{ number_format($todayCashCollected, 2) }} ETB paid</span>
                    </div>
                </div>
            </div>

            <!-- Inventory Buying Total vs Selling Total -->
            <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-600">Stock Retail Value</span>
                    <div class="w-9 h-9 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                        <i data-lucide="boxes" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($valuationAtRetail, 2) }}</span>
                    <span class="text-xs font-bold text-slate-500"> ETB</span>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                        <span>Buying Cost: <strong class="text-indigo-900">{{ number_format($valuationAtCost, 2) }} ETB</strong></span>
                        <a href="{{ route('info.index') }}" class="font-black text-indigo-700 hover:underline">Details &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Total In-Stock Units -->
            <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-600">In-Stock Units</span>
                    <div class="w-9 h-9 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-black text-slate-900">{{ number_format($totalStockUnits) }}</span>
                    <span class="text-xs font-bold text-slate-500"> Units</span>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                        <span>Across {{ $totalProductsCount }} products</span>
                        <a href="{{ route('info.index') }}" class="font-black text-slate-700 hover:underline">Catalog &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Stock Health Alert Status -->
            <div class="glass-card rounded-3xl p-5 border-2 border-slate-200 bg-white shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-black uppercase tracking-wider text-slate-600">Stock Health</span>
                    <div class="w-9 h-9 rounded-2xl {{ ($lowStockCount + $outOfStockCount) > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center">
                        <i data-lucide="{{ ($lowStockCount + $outOfStockCount) > 0 ? 'alert-triangle' : 'check' }}" class="w-5 h-5"></i>
                    </div>
                </div>
                <div>
                    @if(($lowStockCount + $outOfStockCount) > 0)
                        <span class="text-2xl sm:text-3xl font-black text-rose-700">{{ $lowStockCount + $outOfStockCount }} Items</span>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                            <span>{{ $outOfStockCount }} out &bull; {{ $lowStockCount }} low</span>
                            <a href="{{ route('info.index', ['status' => 'low_stock']) }}" class="font-black text-rose-700 hover:underline">Restock &rarr;</a>
                        </div>
                    @else
                        <span class="text-2xl sm:text-3xl font-black text-emerald-700">All Good</span>
                        <div class="mt-2 flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-100 font-bold">
                            <span>Adequate supply</span>
                            <span class="font-black text-emerald-700">Healthy</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 7-Day Revenue Trend Visual Bar -->
        <div class="glass-card rounded-3xl p-6 border-2 border-slate-200 shadow-xs bg-white">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-base font-black text-slate-900">7-Day Sales Trend (ETB)</h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-600">Past 7 Days Total: <strong class="text-slate-900">{{ number_format(collect($salesTrend)->sum('total'), 2) }} ETB</strong></span>
                    <a href="{{ route('reports.financial', ['period' => 'daily']) }}" class="text-xs font-black text-purple-700 hover:underline">View Daily Reports &rarr;</a>
                </div>
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
                        <div class="text-[11px] font-black text-slate-800 mb-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{ number_format($day['total'], 0) }} Br
                        </div>
                        <div class="w-full max-w-[48px] rounded-xl transition-all duration-300 {{ $isToday ? 'bg-gradient-to-t from-emerald-600 to-teal-500 shadow-md shadow-emerald-500/20' : 'bg-slate-200 hover:bg-slate-300' }}" style="height: {{ $percentage }}%;"></div>
                        <span class="text-[11px] font-bold mt-2 {{ $isToday ? 'text-emerald-700 font-extrabold' : 'text-slate-600' }}">{{ $day['day'] }}</span>
                        <span class="text-[10px] text-slate-400 font-semibold">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 2-Column Tables Section: Recent Sales & Low Stock Watchlist -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left: Today's Recent Sales Invoices (7 Cols) -->
            <div class="lg:col-span-7 glass-card rounded-3xl p-6 border-2 border-slate-200 shadow-xs bg-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <i data-lucide="receipt" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Latest Sales Invoices</h3>
                            </div>
                        </div>
                        <a href="{{ route('sales.index') }}" class="text-xs font-black text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                            <span>View All</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>

                    @if($recentSales->isEmpty())
                        <div class="py-12 text-center text-slate-400 text-xs">
                            <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                            <p class="font-bold text-slate-600">No sales recorded yet today.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($recentSales as $sale)
                                <div class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/70 rounded-xl px-2 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-black text-xs">
                                            #
                                        </div>
                                        <div>
                                            <a href="{{ route('sales.show', $sale) }}" class="text-xs font-black text-slate-900 hover:text-emerald-700 block">
                                                {{ $sale->invoice_number }}
                                            </a>
                                            <span class="text-[11px] text-slate-500 font-semibold">
                                                {{ $sale->customer->name ?? 'Walk-In Customer' }} &bull; {{ $sale->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <span class="text-xs font-black text-slate-900 block">{{ number_format($sale->grand_total, 2) }} ETB</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black {{ $sale->payment_status === 'PAID' ? 'bg-emerald-100 text-emerald-800' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $sale->payment_status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 mt-3 text-center">
                    <a href="{{ route('sales.create') }}" class="text-xs font-black text-emerald-700 hover:underline">
                        + Start Point of Sale (POS) &rarr;
                    </a>
                </div>
            </div>

            <!-- Right: Low Stock Watchlist (5 Cols) -->
            <div class="lg:col-span-5 glass-card rounded-3xl p-6 border-2 border-slate-200 shadow-xs bg-white flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">Low Stock Watchlist</h3>
                            </div>
                        </div>
                        <a href="{{ route('info.index', ['status' => 'low_stock']) }}" class="text-xs font-black text-amber-700 hover:text-amber-800 flex items-center gap-1">
                            <span>Manage</span>
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>

                    @if($lowStockProducts->isEmpty())
                        <div class="py-12 text-center text-slate-400 text-xs">
                            <i data-lucide="check-circle-2" class="w-8 h-8 mx-auto text-emerald-500 mb-2"></i>
                            <p class="font-bold text-slate-700">All products are adequately stocked.</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($lowStockProducts as $prod)
                                <div class="py-2.5 flex items-center justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-black text-slate-900 truncate">{{ $prod->name }}</p>
                                        <p class="text-[11px] text-slate-500 font-mono font-bold">{{ $prod->sku }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-black {{ $prod->current_stock <= 0 ? 'text-rose-700' : 'text-amber-700' }}">
                                            {{ $prod->current_stock }} {{ $prod->unit }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400 font-semibold">Min: {{ $prod->minimum_stock_level }}</span>
                                    </div>
                                    <a href="{{ route('add.index', ['tab' => 'product']) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-black shrink-0">
                                        + Restock
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 mt-3 text-center">
                    <a href="{{ route('add.index', ['tab' => 'product']) }}" class="text-xs font-black text-indigo-700 hover:underline">
                        + Add New Product to Inventory &rarr;
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
