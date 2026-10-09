<x-layouts.app>
    <x-slot:title>New Stock-In Purchase</x-slot:title>
    <x-slot:headerTitle>Procurement Operations</x-slot:headerTitle>
    <x-slot:headerSubtitle>Create Purchase Order (Stock-In)</x-slot:headerSubtitle>

    <div class="max-w-5xl mx-auto" x-data="purchaseForm()">
        <form method="POST" action="{{ route('purchases.store') }}" class="space-y-6">
            @csrf

            <!-- Header Card: Supplier & Dates -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Supplier & Order Information</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="supplier_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Supplier *</label>
                        <select id="supplier_id" name="supplier_id" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Vendor</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->company_name ?? $sup->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="purchase_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Purchase / Delivery Date *</label>
                        <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Vendor Ref / Bill #</label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}" placeholder="e.g. Vendor Invoice #8841" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Dynamic Line Items Builder Card -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Purchased Products (Stock-In Items)</h2>
                    <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Add Line Item</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-1 w-full">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Product</label>
                                <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="onProductSelect(item)" required class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Choose product...</option>
                                    <template x-for="p in catalog" :key="p.id">
                                        <option :value="p.id" x-text="`${p.name} (SKU: ${p.sku})`"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="w-full sm:w-28">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Qty</label>
                                <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" min="1" required class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-sm text-center font-bold focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="w-full sm:w-36">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Unit Cost ($)</label>
                                <input type="number" step="0.01" :name="`items[${index}][unit_cost]`" x-model.number="item.unit_cost" min="0" required class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-sm font-semibold focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="w-full sm:w-32 text-right">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Subtotal</label>
                                <span class="text-sm font-bold text-slate-900 block py-2" x-text="`$${(item.quantity * item.unit_cost).toFixed(2)}`"></span>
                            </div>

                            <div class="pt-4 sm:pt-0">
                                <button type="button" @click="removeItem(index)" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100" :disabled="items.length <= 1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Calculation & Payment Card -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <!-- Additional Charges -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-slate-900">Additional Charges & Supplier Discounts</h3>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tax / VAT ($)</label>
                                <input type="number" step="0.01" name="tax_amount" x-model.number="tax_amount" min="0" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Shipping Cost ($)</label>
                                <input type="number" step="0.01" name="shipping_cost" x-model.number="shipping_cost" min="0" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Supplier Discount ($)</label>
                            <input type="number" step="0.01" name="discount_amount" x-model.number="discount_amount" min="0" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-sm">
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Initial Settlement</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">Amount Paid ($)</label>
                                    <input type="number" step="0.01" name="amount_paid" x-model.number="amount_paid" min="0" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-sm font-bold text-emerald-600">
                                </div>
                                <div>
                                    <label class="block text-xs text-slate-600 mb-1">Method</label>
                                    <select name="payment_method" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-sm">
                                        <option value="CASH">Cash</option>
                                        <option value="BANK_TRANSFER">Bank Transfer</option>
                                        <option value="CHEQUE">Cheque</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary Breakdown -->
                    <div class="p-6 rounded-2xl bg-slate-900 text-slate-100 flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm text-slate-400">
                                <span>Line Items Subtotal:</span>
                                <span class="font-mono font-medium text-white" x-text="`$${calculateSubtotal().toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-400">
                                <span>Tax & Freight:</span>
                                <span class="font-mono font-medium text-white" x-text="`+$${(tax_amount + shipping_cost).toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between text-sm text-slate-400">
                                <span>Discount:</span>
                                <span class="font-mono font-medium text-amber-400" x-text="`-$${discount_amount.toFixed(2)}`"></span>
                            </div>
                            <div class="pt-3 border-t border-slate-800 flex justify-between text-lg font-bold">
                                <span>Grand Total:</span>
                                <span class="font-mono text-indigo-400" x-text="`$${calculateGrandTotal().toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between text-sm text-rose-400 pt-1">
                                <span>Remaining Balance (Debt):</span>
                                <span class="font-mono font-bold" x-text="`$${Math.max(0, calculateGrandTotal() - amount_paid).toFixed(2)}`"></span>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all">
                                Confirm & Update Inventory Stock
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function purchaseForm() {
            return {
                catalog: @json($products),
                items: [
                    {
                        product_id: '{{ $selectedProductId ?? "" }}',
                        quantity: 1,
                        unit_cost: {{ $selectedProductId ? ($products->firstWhere('id', $selectedProductId)->cost_price ?? 0) : 0 }}
                    }
                ],
                tax_amount: 0,
                shipping_cost: 0,
                discount_amount: 0,
                amount_paid: 0,

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_cost: 0 });
                    this.$nextTick(() => lucide.createIcons());
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                onProductSelect(item) {
                    const prod = this.catalog.find(p => p.id == item.product_id);
                    if (prod) {
                        item.unit_cost = parseFloat(prod.cost_price);
                    }
                },
                calculateSubtotal() {
                    return this.items.reduce((sum, item) => sum + ((item.quantity || 0) * (item.unit_cost || 0)), 0);
                },
                calculateGrandTotal() {
                    return Math.max(0, this.calculateSubtotal() + (this.tax_amount || 0) + (this.shipping_cost || 0) - (this.discount_amount || 0));
                }
            }
        }
    </script>
</x-layouts.app>
