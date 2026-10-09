<x-layouts.app>
    <x-slot:title>{{ $customer->name }}</x-slot:title>
    <x-slot:headerTitle>Commercial Relations</x-slot:headerTitle>
    <x-slot:headerSubtitle>Customer Account Statement</x-slot:headerSubtitle>

    <div class="space-y-8 max-w-5xl mx-auto">
        <!-- Customer Profile Card -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900">{{ $customer->name }}</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $customer->phone ?? 'No phone' }} &bull; {{ $customer->email ?? 'No email' }}</p>
                    @if($customer->address)
                        <p class="text-xs text-slate-400 mt-1">{{ $customer->address }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('sales.create', ['customer_id' => $customer->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                        <span>New Sale</span>
                    </a>
                    @if($customer->outstanding_balance > 0)
                        <a href="{{ route('payments.customer.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            <span>Collect Payment</span>
                        </a>
                    @endif
                    <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                        <span>Edit</span>
                    </a>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="grid grid-cols-3 gap-4 mt-8 pt-6 border-t border-slate-100">
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Invoiced</span>
                    <p class="text-xl font-black text-slate-900 mt-1">${{ number_format($customer->total_sales, 2) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Collected</span>
                    <p class="text-xl font-black text-emerald-600 mt-1">${{ number_format($customer->total_paid, 2) }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Outstanding Receivables</span>
                    <p class="text-xl font-black text-amber-600 mt-1">${{ number_format($customer->outstanding_balance, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Sales Invoices -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Sales & Invoice History</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">Invoice #</th>
                            <th class="px-4 py-3">Sale Date</th>
                            <th class="px-4 py-3 text-right">Grand Total</th>
                            <th class="px-4 py-3 text-right">Amount Paid</th>
                            <th class="px-4 py-3 text-right">Remaining Balance</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right rounded-r-xl">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-bold text-slate-900 font-mono text-xs">{{ $sale->invoice_number }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $sale->sale_date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-right font-medium text-slate-800">${{ number_format($sale->grand_total, 2) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-emerald-600">${{ number_format($sale->amount_paid, 2) }}</td>
                                <td class="px-4 py-3 text-right font-bold {{ $sale->remaining_balance > 0 ? 'text-amber-600' : 'text-slate-400' }}">${{ number_format($sale->remaining_balance, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $sale->payment_status === 'PAID' ? 'badge-paid' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'badge-partial' : 'badge-unpaid') }}">
                                        {{ $sale->payment_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('sales.show', $sale) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-colors">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">No sales recorded for this customer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sales->hasPages())
                <div class="mt-4 pt-4 border-t border-slate-100">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
