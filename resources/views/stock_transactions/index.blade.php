<x-layouts.app>
    <x-slot:title>Movement Ledger</x-slot:title>
    <x-slot:headerTitle>Inventory Audit</x-slot:headerTitle>
    <x-slot:headerSubtitle>Immutable Stock Movement Ledger</x-slot:headerSubtitle>

    <div class="space-y-6">
        <!-- Filter & Adjustment Action Bar -->
        <div class="glass-card rounded-3xl p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('stock-transactions.index') }}" class="flex-1 flex flex-wrap items-center gap-3">
                <select name="type" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700">
                    <option value="">All Movement Types</option>
                    <option value="PURCHASE" {{ request('type') === 'PURCHASE' ? 'selected' : '' }}>Purchase (Stock-In)</option>
                    <option value="SALE" {{ request('type') === 'SALE' ? 'selected' : '' }}>Sale (Stock-Out)</option>
                    <option value="ADJUSTMENT_ADD" {{ request('type') === 'ADJUSTMENT_ADD' ? 'selected' : '' }}>Adjustment (+)</option>
                    <option value="ADJUSTMENT_SUB" {{ request('type') === 'ADJUSTMENT_SUB' ? 'selected' : '' }}>Adjustment (-)</option>
                </select>

                <select name="product_id" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700">
                    <option value="">All Products</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all">Filter Ledger</button>
            </form>

            <a href="{{ route('stock-adjustments.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-xs">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                <span>Manual Stock Adjustment</span>
            </a>
        </div>

        <!-- Ledger Table -->
        <div class="glass-card rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-6 py-4">Timestamp</th>
                            <th class="px-6 py-4">Product Name & SKU</th>
                            <th class="px-6 py-4">Movement Type</th>
                            <th class="px-6 py-4 text-center">Change Qty</th>
                            <th class="px-6 py-4 text-center">Before</th>
                            <th class="px-6 py-4 text-center">After</th>
                            <th class="px-6 py-4">Reason / Reference</th>
                            <th class="px-6 py-4 text-right">Authorized By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                    {{ $tx->created_at->format('M d, Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    <a href="{{ route('products.show', $tx->product_id) }}" class="hover:text-indigo-600">
                                        {{ $tx->product->name ?? 'Deleted Product' }}
                                    </a>
                                    <span class="block font-mono text-xs text-slate-400">SKU: {{ $tx->product->sku ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                    <span class="px-2.5 py-1 rounded-md bg-slate-100">{{ $tx->formatted_type }}</span>
                                </td>
                                <td class="px-6 py-4 text-center font-bold {{ $tx->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->quantity > 0 ? '+' : '' }}{{ $tx->quantity }}
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500 font-mono text-xs">{{ $tx->balance_before }}</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-900 font-mono text-xs">{{ $tx->balance_after }}</td>
                                <td class="px-6 py-4 text-xs text-slate-600 max-w-xs truncate">{{ $tx->reason ?? '—' }}</td>
                                <td class="px-6 py-4 text-right text-xs text-slate-500">{{ $tx->user->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    No stock ledger transactions recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
