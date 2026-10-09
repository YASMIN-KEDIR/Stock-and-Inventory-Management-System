<x-layouts.app>
    <x-slot:title>Payments & Recovery</x-slot:title>
    <x-slot:headerTitle>Credit & Payments</x-slot:headerTitle>
    <x-slot:headerSubtitle>Multi-Installment Debt Recovery & Settlements</x-slot:headerSubtitle>

    <div class="space-y-6">
        <!-- Tab Navigation & Action Hub -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2 p-1.5 rounded-2xl bg-slate-200/60 w-fit">
                <a href="{{ route('payments.index', ['tab' => 'customer']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'customer' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Customer Payments (Receivables)
                </a>
                <a href="{{ route('payments.index', ['tab' => 'supplier']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $tab === 'supplier' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Supplier Payments (Payables)
                </a>
            </div>

            <div class="flex items-center gap-3">
                @if($tab === 'customer')
                    <a href="{{ route('payments.customer.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-xs">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Collect Customer Payment</span>
                    </a>
                @else
                    <a href="{{ route('payments.supplier.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Issue Supplier Payment</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Payments Table -->
        <div class="glass-card rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-6 py-4">Receipt / Voucher #</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">{{ $tab === 'customer' ? 'Customer' : 'Supplier' }}</th>
                            <th class="px-6 py-4">Reference / Invoice</th>
                            <th class="px-6 py-4">Payment Method</th>
                            <th class="px-6 py-4 text-right">Amount Paid</th>
                            <th class="px-6 py-4 text-right">Processed By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $pay)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-slate-900 text-xs">
                                    {{ $pay->payment_number }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $pay->payment_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-800">
                                    @if($tab === 'customer')
                                        {{ $pay->customer->name ?? 'Walk-In Customer' }}
                                    @else
                                        {{ $pay->supplier->company_name ?? $pay->supplier->name }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-indigo-600 font-semibold">
                                    @if($tab === 'customer')
                                        {{ $pay->sale->invoice_number ?? 'General' }}
                                    @else
                                        {{ $pay->purchase->purchase_number ?? 'General' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                    <span class="px-2.5 py-1 rounded-md bg-slate-100">{{ $pay->payment_method }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-emerald-600 text-sm">
                                    ${{ number_format($pay->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs text-slate-500">
                                    {{ $pay->user->name ?? 'Admin' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    No payment transactions found in this category.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
