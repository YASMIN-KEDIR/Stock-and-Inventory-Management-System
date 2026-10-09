<x-layouts.app>
    <x-slot:title>Issue Supplier Payment</x-slot:title>
    <x-slot:headerTitle>Supply Chain Debt</x-slot:headerTitle>
    <x-slot:headerSubtitle>Supplier Purchase Settlement</x-slot:headerSubtitle>

    <div class="max-w-2xl mx-auto" x-data="supplierPaymentForm()">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Issue Supplier Settlement Payment</h2>

            <form method="POST" action="{{ route('payments.supplier.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="purchase_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Select Outstanding Purchase Order *</label>
                    <select id="purchase_id" name="purchase_id" x-model="selectedPurchaseId" @change="updateMaxAmount()" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choose Purchase Order with Payable Balance</option>
                        @foreach($unpaidPurchases as $p)
                            <option value="{{ $p->id }}" {{ (old('purchase_id') ?? ($purchase->id ?? '')) == $p->id ? 'selected' : '' }}>
                                {{ $p->purchase_number }} &bull; {{ $p->supplier->company_name ?? $p->supplier->name }} (Owed: ${{ number_format($p->remaining_balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('purchase_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs" x-show="currentBalance > 0">
                    <span class="font-bold">Total Payable Balance Owed:</span>
                    <span class="font-mono text-sm font-extrabold ml-1" x-text="`$${currentBalance.toFixed(2)}`"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="payment_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Date *</label>
                        <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Disbursement Amount ($) *</label>
                        <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" required min="0.01" :max="currentBalance || 999999" placeholder="0.00" onfocus="this.select()" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('amount') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Disbursement Method *</label>
                        <select id="payment_method" name="payment_method" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="CASH">Cash Voucher</option>
                            <option value="BANK_TRANSFER">Bank Wire Transfer</option>
                            <option value="CHEQUE">Corporate Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label for="reference_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Cheque / Wire Slip #</label>
                        <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g. Wire Confirmation #7712" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Remarks</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Settlement details..." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('payments.index', ['tab' => 'supplier']) }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">Record Supplier Settlement</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function supplierPaymentForm() {
            return {
                unpaidPurchases: @json($unpaidPurchases),
                selectedPurchaseId: '{{ old("purchase_id") ?? ($purchase->id ?? "") }}',
                currentBalance: 0,

                init() {
                    this.updateMaxAmount();
                },

                updateMaxAmount() {
                    const found = this.unpaidPurchases.find(p => p.id == this.selectedPurchaseId);
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
