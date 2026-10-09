<x-layouts.app>
    <x-slot:title>Merkato Daily Store Summary</x-slot:title>
    <x-slot:headerTitle>Store Summary</x-slot:headerTitle>
    <x-slot:headerSubtitle>Daily Sales & Cashier Hub</x-slot:headerSubtitle>

    <div class="space-y-8">

        <!-- Cashier Quick Launch Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 border border-slate-800 p-6 sm:p-8 text-white shadow-xl shadow-slate-950/20">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-bold mb-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Merkato POS Counter Ready
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                        Store Cashier Dashboard
                    </h1>
                    <p class="text-slate-300 text-sm mt-1 max-w-xl">
                        Everything you need to sell items, collect cash, check stock, and manage shop balances.
                    </p>
                </div>

                <!-- Big Primary Action Hub for Cashier -->
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:brightness-110 text-white font-black text-sm shadow-xl shadow-emerald-600/30 transition-all hover:scale-105 active:scale-95">
                        <i data-lucide="zap" class="w-5 h-5 text-amber-300 animate-pulse"></i>
                        <span>⚡ Open Cashier POS (Sell)</span>
                    </a>
                    <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition-all hover:scale-105 active:scale-95">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                        <span>Stock-In</span>
                    </a>
                    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition-all hover:scale-105 active:scale-95">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>New Product</span>
                    </a>
                </div>
            </div>

            <!-- Glow Effect -->
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- 4 Big Financial & Stock Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Today's Total Sales -->
            <div class="glass-card rounded-2xl p-5 shadow-xs border border-slate-200/80 bg-white hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Today's Sales</span>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="badge-dollar-sign" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">${{ number_format($todaySales, 2) }}</span>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="font-bold text-emerald-600">${{ number_format($todayCashCollected, 2) }}</span> cash in drawer
                    </p>
                </div>
            </div>

            <!-- Customer Credit / Money Owed by Customers -->
            <div class="glass-card rounded-2xl p-5 shadow-xs border border-slate-200/80 bg-white hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Customer Debt to Collect</span>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-amber-600">${{ number_format($totalCustomerReceivables, 2) }}</span>
                    <p class="text-xs text-slate-500 mt-1">
                        Unpaid customer balance
                    </p>
                </div>
            </div>

            <!-- In-Store Stock Value -->
            <div class="glass-card rounded-2xl p-5 shadow-xs border border-slate-200/80 bg-white hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">In-Store Stock Value</span>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-slate-900">${{ number_format($valuationAtRetail, 2) }}</span>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <span class="font-medium text-slate-600">${{ number_format($valuationAtCost, 2) }}</span> wholesale cost
                    </p>
                </div>
            </div>

            <!-- Supplier Bills Due -->
            <div class="glass-card rounded-2xl p-5 shadow-xs border border-slate-200/80 bg-white hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Supplier Bills Due</span>
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-2xl font-black text-rose-600">${{ number_format($totalSupplierPayables, 2) }}</span>
                    <p class="text-xs text-slate-500 mt-1">
                        Due to pay suppliers
                    </p>
                </div>
            </div>
        </div>

        <!-- 4 Quick Large Shortcut Cards for Everyday Cashier Tasks -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('sales.create') }}" class="p-5 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-md hover:scale-[1.02] transition-transform flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center font-bold text-xl">⚡</div>
                <div>
                    <h3 class="font-black text-base">Make a Sale</h3>
                    <p class="text-xs text-emerald-100">Sell products & print receipt</p>
                </div>
            </a>

            <a href="{{ route('payments.index') }}" class="p-5 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white shadow-md hover:scale-[1.02] transition-transform flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center font-bold text-xl">💰</div>
                <div>
                    <h3 class="font-black text-base">Collect Debt</h3>
                    <p class="text-xs text-indigo-100">Record customer cash payment</p>
                </div>
            </a>

            <a href="{{ route('purchases.create') }}" class="p-5 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-md hover:scale-[1.02] transition-transform flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center font-bold text-xl">📥</div>
                <div>
                    <h3 class="font-black text-base">Receive Stock</h3>
                    <p class="text-xs text-slate-300">Add inventory from suppliers</p>
                </div>
            </a>

            <a href="{{ route('products.create') }}" class="p-5 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 text-white shadow-md hover:scale-[1.02] transition-transform flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center font-bold text-xl">🏷️</div>
                <div>
                    <h3 class="font-black text-base">New Item</h3>
                    <p class="text-xs text-amber-100">Create new item with barcode</p>
                </div>
            </a>
        </div>

        <!-- Low Stock Items Warning Card -->
        <div class="glass-card rounded-3xl p-6 border border-slate-200/80 shadow-xs bg-white">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Low Stock Warning (Reorder Needed)</h2>
                        <p class="text-xs text-slate-500">Items running out of stock soon</p>
                    </div>
                </div>
                <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    <span>View All Products</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            @if($lowStockProducts->isEmpty())
                <div class="py-8 text-center text-slate-500">
                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500 mx-auto mb-2"></i>
                    <p class="text-sm font-semibold text-slate-700">All products in the shop are well-stocked.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-wider font-bold rounded-xl">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Product Name</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3 text-center">Remaining Stock</th>
                                <th class="px-4 py-3 text-center">Safety Level</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right rounded-r-xl">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($lowStockProducts as $prod)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3.5 font-bold text-slate-900">{{ $prod->name }}</td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-slate-600">{{ $prod->sku }}</td>
                                    <td class="px-4 py-3.5 text-center font-black text-rose-600">{{ $prod->current_stock }} {{ $prod->unit }}</td>
                                    <td class="px-4 py-3.5 text-center text-slate-500 font-medium">{{ $prod->minimum_stock_level }} {{ $prod->unit }}</td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if($prod->current_stock <= 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold badge-outofstock">
                                                Out of Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold badge-lowstock">
                                                Low Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <a href="{{ route('purchases.create', ['product_id' => $prod->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition-colors">
                                            <i data-lucide="truck" class="w-3.5 h-3.5"></i>
                                            <span>Add Stock</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Recent Sales Table -->
        <div class="glass-card rounded-3xl p-6 border border-slate-200/80 shadow-xs bg-white">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="receipt" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Today's Recent Invoices</h2>
                        <p class="text-xs text-slate-500">Sales made at checkout</p>
                    </div>
                </div>
                <a href="{{ route('sales.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                    <span>View All Invoices</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            @if($recentSales->isEmpty())
                <div class="py-8 text-center text-slate-500 text-sm">
                    No sales made yet today. Click <a href="{{ route('sales.create') }}" class="text-emerald-600 font-bold underline">⚡ Open Cashier POS</a> to make your first sale.
                </div>
            @else
                <div class="space-y-3">
                    @foreach($recentSales as $sale)
                        <div class="p-3.5 rounded-2xl bg-slate-50 hover:bg-slate-100/80 transition-colors flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-900 text-sm">{{ $sale->invoice_number }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sale->payment_status === 'PAID' ? 'badge-paid' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'badge-partial' : 'badge-unpaid') }}">
                                        {{ $sale->payment_status }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5 font-medium">
                                    {{ $sale->customer->name ?? 'Walk-In Customer' }} &bull; {{ $sale->sale_date->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="font-black text-slate-900 text-sm block">${{ number_format($sale->grand_total, 2) }}</span>
                                @if($sale->remaining_balance > 0)
                                    <span class="text-[11px] font-bold text-amber-600">Credit Due: ${{ number_format($sale->remaining_balance, 2) }}</span>
                                @else
                                    <span class="text-[11px] font-bold text-emerald-600">✓ Fully Paid</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>
