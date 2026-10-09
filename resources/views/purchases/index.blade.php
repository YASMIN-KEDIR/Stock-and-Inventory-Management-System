<x-layouts.app>
    <x-slot:title>Purchases (Stock-In)</x-slot:title>
    <x-slot:headerTitle>Procurement Operations</x-slot:headerTitle>
    <x-slot:headerSubtitle>Purchase Orders & Stock Inbound Log</x-slot:headerSubtitle>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('purchases.index') }}" class="flex-1 max-w-md">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search PO # or supplier..." class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </form>

            <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">
                <i data-lucide="truck" class="w-4 h-4"></i>
                <span>Create Stock-In PO</span>
            </a>
        </div>

        <div class="glass-card rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-6 py-4">PO Number</th>
                            <th class="px-6 py-4">Supplier</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4 text-right">Grand Total</th>
                            <th class="px-6 py-4 text-right">Amount Paid</th>
                            <th class="px-6 py-4 text-right">Remaining Debt</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($purchases as $po)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900 font-mono text-xs">
                                    <a href="{{ route('purchases.show', $po) }}" class="hover:text-indigo-600">
                                        {{ $po->purchase_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    {{ $po->supplier->company_name ?? $po->supplier->name }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $po->purchase_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900">
                                    ${{ number_format($po->grand_total, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-emerald-600">
                                    ${{ number_format($po->amount_paid, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold {{ $po->remaining_balance > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                    ${{ number_format($po->remaining_balance, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $po->payment_status === 'PAID' ? 'badge-paid' : ($po->payment_status === 'PARTIALLY_PAID' ? 'badge-partial' : 'badge-unpaid') }}">
                                        {{ $po->payment_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('purchases.show', $po) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-colors" title="View Purchase Details">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    No purchase orders recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($purchases->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
