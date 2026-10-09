<x-layouts.app>
    <x-slot:title>Invoice {{ $sale->invoice_number }}</x-slot:title>
    <x-slot:headerTitle>Commercial Operations</x-slot:headerTitle>
    <x-slot:headerSubtitle>Sales Invoice Overview</x-slot:headerSubtitle>

    <div class="space-y-8 max-w-4xl mx-auto">
        <!-- Top Invoice Header -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-black font-mono text-slate-900">{{ $sale->invoice_number }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $sale->payment_status === 'PAID' ? 'badge-paid' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'badge-partial' : 'badge-unpaid') }}">
                            {{ $sale->payment_status }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">
                        Date: <strong class="text-slate-800">{{ $sale->sale_date->format('F d, Y') }}</strong> &bull; Cashier: {{ $sale->user->name ?? 'Admin' }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('sales.print', $sale) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Print Invoice</span>
                    </a>
                    @if($sale->remaining_balance > 0)
                        <a href="{{ route('payments.customer.create', ['sale_id' => $sale->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            <span>Collect Payment</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Customer & Financial Snapshot -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Customer Details</span>
                    <p class="font-bold text-slate-900 text-sm mt-1">{{ $sale->customer->name ?? 'Walk-In Customer (Cash)' }}</p>
                    <p class="text-xs text-slate-500">{{ $sale->customer->phone ?? 'No phone' }} &bull; {{ $sale->customer->email ?? 'No email' }}</p>
                    @if($sale->customer && $sale->customer->address)
                        <p class="text-xs text-slate-400 mt-1">{{ $sale->customer->address }}</p>
                    @endif
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Financial & Profit Summary</span>
                    <div class="flex justify-between text-xs mt-1">
                        <span class="text-slate-600">Grand Total (Revenue):</span>
                        <span class="font-bold text-slate-900">${{ number_format($sale->grand_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs mt-0.5">
                        <span class="text-slate-600">Total Buying Cost:</span>
                        <span class="font-medium text-slate-500">${{ number_format($sale->total_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs mt-0.5 pt-1 border-t border-slate-200/60">
                        <span class="text-emerald-700 font-bold">Gross Profit Earned:</span>
                        <span class="font-black text-emerald-600">+${{ number_format($sale->gross_profit, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs mt-1">
                        <span class="text-slate-600">Amount Collected:</span>
                        <span class="font-bold text-slate-800">${{ number_format($sale->amount_paid, 2) }}</span>
                    </div>
                    @if($sale->remaining_balance > 0)
                        <div class="flex justify-between text-xs mt-0.5">
                            <span class="text-amber-700 font-bold">Balance Due (Credit):</span>
                            <span class="font-black text-amber-600">${{ number_format($sale->remaining_balance, 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Line Items Table -->
            <div class="mt-8">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Line Items & Profit Breakdown</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Product</th>
                                <th class="px-4 py-3 text-center">Quantity</th>
                                <th class="px-4 py-3 text-right">Buying Cost</th>
                                <th class="px-4 py-3 text-right">Sold Price</th>
                                <th class="px-4 py-3 text-right">Profit Earned</th>
                                <th class="px-4 py-3 text-right rounded-r-xl">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">
                                        {{ $item->product->name ?? 'Product' }}
                                        <span class="block font-mono text-[11px] text-slate-400">SKU: {{ $item->product->sku ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">${{ number_format($item->cost_price_at_sale, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-black {{ $item->margin >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $item->margin >= 0 ? '+' : '' }}${{ number_format($item->margin, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-slate-900">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Installment Payments -->
        @if($sale->payments->isNotEmpty())
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Payment Receipts Log</h3>
                <div class="space-y-3">
                    @foreach($sale->payments as $pay)
                        <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 font-mono">{{ $pay->payment_number }}</span>
                                <span class="text-slate-500 ml-2">{{ $pay->payment_date->format('M d, Y') }} via {{ $pay->payment_method }}</span>
                            </div>
                            <span class="font-bold text-emerald-600 text-sm">${{ number_format($pay->amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
