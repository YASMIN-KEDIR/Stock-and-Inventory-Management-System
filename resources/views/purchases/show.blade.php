<x-layouts.app>
    <x-slot:title>PO #{{ $purchase->purchase_number }}</x-slot:title>
    <x-slot:headerTitle>Procurement Operations</x-slot:headerTitle>
    <x-slot:headerSubtitle>Purchase Order Details</x-slot:headerSubtitle>

    <div class="space-y-8 max-w-4xl mx-auto">
        <!-- Order Header Card -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-6 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-black font-mono text-slate-900">{{ $purchase->purchase_number }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $purchase->payment_status === 'PAID' ? 'badge-paid' : ($purchase->payment_status === 'PARTIALLY_PAID' ? 'badge-partial' : 'badge-unpaid') }}">
                            {{ $purchase->payment_status }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">
                        Date: <strong class="text-slate-800">{{ $purchase->purchase_date->format('F d, Y') }}</strong> &bull; Recorded by: {{ $purchase->user->name ?? 'Admin' }}
                    </p>
                </div>

                @if($purchase->remaining_balance > 0)
                    <a href="{{ route('payments.supplier.create', ['purchase_id' => $purchase->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>Record Supplier Payment</span>
                    </a>
                @endif
            </div>

            <!-- Supplier Info Box -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Supplier / Vendor</span>
                    <p class="font-bold text-slate-900 text-sm mt-1">{{ $purchase->supplier->company_name ?? $purchase->supplier->name }}</p>
                    <p class="text-xs text-slate-500">{{ $purchase->supplier->phone }} &bull; {{ $purchase->supplier->email ?? 'No email' }}</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Financial Summary</span>
                    <div class="flex justify-between text-xs mt-1">
                        <span class="text-slate-600">Grand Total:</span>
                        <span class="font-bold text-slate-900">${{ number_format($purchase->grand_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs mt-0.5">
                        <span class="text-slate-600">Total Settled:</span>
                        <span class="font-bold text-emerald-600">${{ number_format($purchase->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs mt-0.5">
                        <span class="text-slate-600">Balance Owed:</span>
                        <span class="font-bold text-rose-600">${{ number_format($purchase->remaining_balance, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Line Items Table -->
            <div class="mt-8">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Received Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-4 py-3 rounded-l-xl">Product</th>
                                <th class="px-4 py-3 text-center">Quantity</th>
                                <th class="px-4 py-3 text-right">Unit Cost</th>
                                <th class="px-4 py-3 text-right rounded-r-xl">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($purchase->items as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-900">
                                        {{ $item->product->name ?? 'Product' }}
                                        <span class="block font-mono text-[11px] text-slate-400">SKU: {{ $item->product->sku ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-slate-600">${{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-slate-900">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supplier Payment Receipts History -->
        @if($purchase->payments->isNotEmpty())
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Payment Receipts Log</h3>
                <div class="space-y-3">
                    @foreach($purchase->payments as $pay)
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
