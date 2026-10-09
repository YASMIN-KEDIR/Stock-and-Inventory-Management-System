<x-layouts.app>
    <x-slot:title>Collect Customer Payment</x-slot:title>
    <x-slot:headerTitle>Credit Management</x-slot:headerTitle>
    <x-slot:headerSubtitle>Customer Debt Installment Receipt</x-slot:headerSubtitle>

    <div class="max-w-2xl mx-auto" x-data="customerPaymentForm()">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Record Customer Debt Payment</h2>

            <form method="POST" action="{{ route('payments.customer.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="sale_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Select Unpaid Sales Invoice *</label>
                    <select id="sale_id" name="sale_id" x-model="selectedSaleId" @change="updateMaxAmount()" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choose Invoice with Outstanding Balance</option>
                        @foreach($unpaidSales as $s)
                            <option value="{{ $s->id }}" {{ (old('sale_id') ?? ($sale->id ?? '')) == $s->id ? 'selected' : '' }}>
                                {{ $s->invoice_number }} &bull; {{ $s->customer->name ?? 'Walk-In Customer' }} (Due: ${{ number_format($s->remaining_balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('sale_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs" x-show="currentBalance > 0">
                    <span class="font-bold">Outstanding Debt on Invoice:</span>
                    <span class="font-mono text-sm font-extrabold ml-1" x-text="`$${currentBalance.toFixed(2)}`"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="payment_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Date *</label>
                        <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Amount ($) *</label>
                        <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" required min="0.01" :max="currentBalance || 999999" placeholder="0.00" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm font-bold text-emerald-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('amount') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Method *</label>
                        <select id="payment_method" name="payment_method" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="CASH">Cash</option>
                            <option value="BANK_TRANSFER">Bank Wire / Transfer</option>
                            <option value="POS_CARD">POS Card Terminal</option>
                            <option value="CHEQUE">Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label for="reference_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Reference / Bank Slip #</label>
                        <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g. Wire Ref #9901" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Receipt Notes</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Partial installment notes..." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('payments.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-xs">Record Payment Receipt</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function customerPaymentForm() {
            return {
                unpaidSales: @json($unpaidSales),
                selectedSaleId: '{{ old("sale_id") ?? ($sale->id ?? "") }}',
                currentBalance: 0,

                init() {
                    this.updateMaxAmount();
                },

                updateMaxAmount() {
                    const found = this.unpaidSales.find(s => s.id == this.selectedSaleId);
                    if (found) {
                        this.currentBalance = parseFloat(found.remaining_balance);
                    } else {
                        this.currentBalance = 0;
                    }
                }
            }
        }
    </script>
</x-layouts.app>
