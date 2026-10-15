<x-layouts.app>
    <x-slot:title>Invoice {{ $sale->invoice_number }}</x-slot:title>
    <x-slot:headerTitle>Commercial Operations</x-slot:headerTitle>
    <x-slot:headerSubtitle>Sales Invoice Overview</x-slot:headerSubtitle>

    <div class="space-y-8 max-w-4xl mx-auto">
        <!-- Top Invoice Header -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs bg-white">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-black font-mono text-slate-900">{{ $sale->invoice_number }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $sale->payment_status === 'PAID' ? 'badge-paid' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'badge-partial' : 'badge-unpaid') }}">
                            {{ $sale->payment_status === 'PAID' ? 'Fully Paid' : ($sale->payment_status === 'PARTIALLY_PAID' ? 'Partial Credit' : 'Unpaid Credit') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">
                        Date: <strong class="text-slate-800">{{ $sale->sale_date->format('F d, Y') }}</strong> &bull; Cashier: {{ $sale->user->name ?? 'Admin' }}
                        @if($sale->payment_method)
                            &bull; Method: <strong class="text-slate-800">
                                @if($sale->payment_method === 'CASH') 💵 Cash in Hand
                                @elseif($sale->payment_method === 'CBE') 🏦 CBE Bank
                                @elseif($sale->payment_method === 'TELEBIRR') 📱 Telebirr
                                @elseif($sale->payment_method === 'ABYSSINIA') 🏛️ Bank of Abyssinia
                                @else {{ $sale->payment_method }}
                                @endif
                            </strong>
                        @endif
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
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Customer Details</span>
                    <p class="font-bold text-slate-900 text-sm mt-1">{{ $sale->customer->name ?? 'Walk-In Customer (Cash Sale)' }}</p>
                    <p class="text-xs text-slate-500">{{ $sale->customer->phone ?? 'No phone' }}</p>
                    @if($sale->customer && $sale->customer->address)
                        <p class="text-xs text-slate-400 mt-1">{{ $sale->customer->address }}</p>
                    @endif
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Financial Summary</span>
                    <div class="flex justify-between text-xs mt-1">
                        <span class="text-slate-600">Total Invoice Amount:</span>
                        <span class="font-black text-slate-900">{{ number_format($sale->grand_total, 2) }} Birr</span>
                    </div>
                    <div class="flex justify-between text-xs mt-0.5">
                        <span class="text-slate-600">Total Buying Cost:</span>
                        <span class="font-medium text-slate-500">{{ number_format($sale->total_cost, 2) }} Birr</span>
                    </div>
                    <div class="flex justify-between text-xs mt-0.5 pt-1 border-t border-slate-200">
                        <span class="text-emerald-700 font-bold">Estimated Profit:</span>
                        <span class="font-black text-emerald-600">+{{ number_format($sale->gross_profit, 2) }} Birr</span>
                    </div>
                    <div class="flex justify-between text-xs mt-1">
                        <span class="text-slate-600">Paid Amount:</span>
                        <span class="font-bold text-emerald-700">{{ number_format($sale->amount_paid, 2) }} Birr</span>
                    </div>
                    @if($sale->remaining_balance > 0)
                        <div class="flex justify-between text-xs mt-0.5 pt-1 border-t border-amber-200 text-amber-900">
                            <span class="font-bold">Remaining Debt:</span>
                            <span class="font-black text-amber-600">{{ number_format($sale->remaining_balance, 2) }} Birr</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Optional Uploaded Receipt Screenshot -->
            @if($sale->receipt_image)
                <div class="mt-6 p-4 rounded-2xl bg-indigo-50/50 border border-indigo-200">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <i data-lucide="image" class="w-4 h-4 text-indigo-600"></i>
                            <span class="text-xs font-black uppercase tracking-wider text-indigo-950">Payment Receipt / Screenshot</span>
                        </div>
                        <a href="{{ asset('storage/' . $sale->receipt_image) }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline flex items-center gap-1">
                            <span>Open Full Size</span>
                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                    </div>
                    <div class="rounded-xl overflow-hidden border border-indigo-200 bg-white inline-block max-w-sm">
                        <a href="{{ asset('storage/' . $sale->receipt_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $sale->receipt_image) }}" alt="Payment Receipt" class="max-h-64 object-contain hover:opacity-95 transition-opacity">
                        </a>
                    </div>
                </div>
            @endif

            <!-- Line Items Table -->
            <div class="mt-8">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Line Items & Profit Breakdown</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider font-bold">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Product</th>
                                <th class="px-4 py-3 text-center">Quantity (Pieces)</th>
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
                                        <span class="block font-mono text-[11px] text-slate-400">Code: {{ $item->product->sku ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-slate-500">{{ number_format($item->cost_price_at_sale, 2) }} Birr</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-800">{{ number_format($item->unit_price, 2) }} Birr</td>
                                    <td class="px-4 py-3 text-right font-black {{ $item->margin >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $item->margin >= 0 ? '+' : '' }}{{ number_format($item->margin, 2) }} Birr
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-slate-900">{{ number_format($item->subtotal, 2) }} Birr</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Installment Payments -->
        @if($sale->payments->isNotEmpty())
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs bg-white">
                <h3 class="text-base font-black text-slate-900 mb-4">Payment Receipts Log</h3>
                <div class="space-y-3">
                    @foreach($sale->payments as $pay)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 font-mono">{{ $pay->payment_number }}</span>
                                <span class="text-slate-500 ml-2">{{ $pay->payment_date->format('M d, Y') }} via {{ $pay->payment_method }}</span>
                                @if($pay->reference_number)
                                    <span class="text-slate-400 font-mono ml-2">Ref: {{ $pay->reference_number }}</span>
                                @endif
                            </div>
                            <span class="font-bold text-emerald-600 text-sm">{{ number_format($pay->amount, 2) }} Birr</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
