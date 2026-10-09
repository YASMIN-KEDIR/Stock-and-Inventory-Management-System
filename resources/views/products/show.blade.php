<x-layouts.app>
    <x-slot:title>{{ $product->name }}</x-slot:title>
    <x-slot:headerTitle>Inventory Management</x-slot:headerTitle>
    <x-slot:headerSubtitle>Product Profile & History</x-slot:headerSubtitle>

    <div class="space-y-8 max-w-5xl mx-auto">
        <!-- Top Details Card -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                        @if($product->current_stock <= 0)
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg badge-outofstock">Out of Stock</span>
                        @elseif($product->current_stock <= $product->minimum_stock_level)
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg badge-lowstock">Low Stock Alert</span>
                        @else
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg badge-instock">In Stock</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900">{{ $product->name }}</h1>
                    <div class="flex items-center gap-4 mt-2 text-xs font-mono text-slate-500">
                        <span>SKU: <strong class="text-slate-800">{{ $product->sku }}</strong></span>
                        @if($product->barcode)
                            <span>Barcode: <strong class="text-slate-800">{{ $product->barcode }}</strong></span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('purchases.create', ['product_id' => $product->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                        <span>Stock-In Order</span>
                    </a>
                    <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                        <span>Edit</span>
                    </a>
                </div>
            </div>

            <!-- Financial & Stock Metrics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-100">
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Current Stock</span>
                    <p class="text-xl font-black text-slate-900 mt-1">{{ $product->current_stock }} <span class="text-sm font-normal text-slate-500">{{ $product->unit }}</span></p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Unit Cost</span>
                    <p class="text-xl font-black text-slate-900 mt-1">${{ number_format($product->cost_price, 2) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Retail Price</span>
                    <p class="text-xl font-black text-indigo-600 mt-1">${{ number_format($product->selling_price, 2) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Stock Valuation</span>
                    <p class="text-xl font-black text-emerald-600 mt-1">${{ number_format($product->stock_valuation_at_retail, 2) }}</p>
                </div>
            </div>

            @if($product->description)
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Specifications</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $product->description }}</p>
                </div>
            @endif
        </div>

        <!-- Recent Double-Entry Stock Movement Ledger -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Stock Movement Audit Ledger</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Date & Time</th>
                            <th class="px-4 py-3">Movement Type</th>
                            <th class="px-4 py-3 text-center">Change Qty</th>
                            <th class="px-4 py-3 text-center">Balance Before</th>
                            <th class="px-4 py-3 text-center">Balance After</th>
                            <th class="px-4 py-3">Reason / Ref</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $tx->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 font-medium text-slate-800">{{ $tx->formatted_type }}</td>
                                <td class="px-4 py-3 text-center font-bold {{ $tx->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->quantity > 0 ? '+' : '' }}{{ $tx->quantity }}
                                </td>
                                <td class="px-4 py-3 text-center text-slate-500">{{ $tx->balance_before }}</td>
                                <td class="px-4 py-3 text-center font-bold text-slate-900">{{ $tx->balance_after }}</td>
                                <td class="px-4 py-3 text-xs text-slate-600 max-w-xs truncate">{{ $tx->reason }}</td>
                                <td class="px-4 py-3 text-right text-xs text-slate-500">{{ $tx->user->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">No stock movements recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
