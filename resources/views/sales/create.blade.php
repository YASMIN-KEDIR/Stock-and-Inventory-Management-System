<x-layouts.app>
    <x-slot:title>Cashier POS Counter</x-slot:title>
    <x-slot:headerTitle>Cashier POS</x-slot:headerTitle>
    <x-slot:headerSubtitle>Fast Checkout & Retail Counter</x-slot:headerSubtitle>

    <div class="max-w-6xl mx-auto" x-data="cashierPos()">
        <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
            @csrf

            <!-- Top Row: Cashier Sale Settings -->
            <div class="glass-card rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs bg-white">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">New Checkout Sale</h2>
                            <p class="text-xs text-slate-500">Fast cashier terminal for daily retail sales</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500 font-medium">Date:</span>
                        <input type="date" id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="customer_id" class="block text-xs font-bold text-slate-700 mb-1.5">Who is buying? (Customer)</label>
                        <select id="customer_id" name="customer_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium focus:ring-2 focus:ring-emerald-500">
                            <option value="">👤 Walk-In Customer (Cash on Counter)</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                    👤 {{ $cust->name }} {{ $cust->phone ? '(' . $cust->phone . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-xs font-bold text-slate-700 mb-1.5">Optional Sale Note / Memo</label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}" placeholder="e.g. Paid in cash, Pickup, Token #4" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm placeholder-slate-400 focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Main Items Table -->
            <div class="glass-card rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs bg-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Items Being Purchased</h2>
                        <p class="text-xs text-slate-500">Pick products and set quantity</p>
                    </div>
                    <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/60 shadow-xs transition-all">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>+ Add Another Product</span>
                    </button>
                </div>

                @if(count($products) === 0)
                    <div class="p-8 text-center rounded-2xl bg-amber-50 border border-amber-200 text-amber-800">
                        <i data-lucide="package-x" class="w-10 h-10 mx-auto text-amber-500 mb-2"></i>
                        <p class="font-bold text-base">No Products in Stock Yet</p>
                        <p class="text-xs text-amber-700 mt-1">Please add products or stock them in first before making sales.</p>
                        <div class="mt-4">
                            <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold shadow-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Add First Product</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col md:flex-row items-center gap-4 transition-all">
                                
                                <!-- Product Picker -->
                                <div class="flex-1 w-full">
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-xs font-bold text-slate-600">Product</label>
                                        <span class="text-[11px] font-semibold text-slate-500" x-show="item.available_stock !== null">
                                            Available in stock: <strong class="text-emerald-700 font-bold" x-text="item.available_stock"></strong>
                                        </span>
                                    </div>
                                    <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="onProductSelect(item)" required class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                                        <option value="">-- Choose Product to Sell --</option>
                                        <template x-for="p in catalog" :key="p.id">
                                            <option :value="p.id" x-text="`${p.name} — $${parseFloat(p.selling_price).toFixed(2)} (In Stock: ${p.current_stock})`"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Quantity with +/- Stepper -->
                                <div class="w-full md:w-40">
                                    <label class="block text-xs font-bold text-slate-600 mb-1 text-center">Quantity</label>
                                    <div class="flex items-center rounded-xl bg-white border border-slate-300 overflow-hidden shadow-xs">
                                        <button type="button" @click="item.quantity = Math.max(1, item.quantity - 1)" class="px-3 py-2 text-slate-600 hover:bg-slate-100 font-black text-base">-</button>
                                        <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" :max="item.available_stock || 9999" min="1" required class="w-full py-2 text-center text-sm font-black text-slate-900 border-x border-slate-200 focus:outline-none">
                                        <button type="button" @click="item.quantity = Math.min(item.available_stock || 9999, item.quantity + 1)" class="px-3 py-2 text-slate-600 hover:bg-slate-100 font-black text-base">+</button>
                                    </div>
                                </div>

                                <!-- Selling Price (Dynamic / Variable Per Sale) -->
                                <div class="w-full md:w-36">
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-xs font-black text-slate-800">Unit Price ($)</label>
                                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-1 rounded">Editable</span>
                                    </div>
                                    <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" min="0" required class="w-full px-3 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-black text-slate-900 focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <!-- Line Discount -->
                                <div class="w-full md:w-28">
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Discount ($)</label>
                                    <input type="number" step="0.01" :name="`items[${index}][line_discount]`" x-model.number="item.line_discount" min="0" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-sm text-amber-700 font-semibold focus:ring-2 focus:ring-emerald-500">
                                </div>

                                <!-- Line Total -->
                                <div class="w-full md:w-32 text-right">
                                    <label class="block text-xs font-bold text-slate-500 mb-1">Total</label>
                                    <span class="text-base font-black text-slate-900 block py-1.5" x-text="`$${Math.max(0, (item.quantity * item.unit_price) - (item.line_discount || 0)).toFixed(2)}`"></span>
                                </div>

                                <!-- Delete Row Button -->
                                <div class="pt-2 md:pt-4">
                                    <button type="button" @click="removeItem(index)" class="p-2 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition-colors" :disabled="items.length <= 1" title="Remove item">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                @endif
            </div>

            <!-- Bottom Checkout & Cashier Settlement Card -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-lg bg-white">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- Left: Cashier Payment Entry -->
                    <div class="lg:col-span-7 space-y-5">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i data-lucide="wallet" class="w-5 h-5 text-emerald-600"></i>
                            <span>Cashier Money Collection</span>
                        </h3>

                        <!-- Quick Cash Tendered Shortcuts -->
                        <div>
                            <span class="block text-xs font-bold text-slate-600 mb-2">Quick Cash Tender Buttons:</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="setExactCash()" class="px-3 py-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-bold border border-emerald-300">
                                    ⚡ Exact Total ($<span x-text="calculateGrandTotal().toFixed(2)"></span>)
                                </button>
                                <button type="button" @click="amount_paid = 10" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300">$10</button>
                                <button type="button" @click="amount_paid = 20" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300">$20</button>
                                <button type="button" @click="amount_paid = 50" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300">$50</button>
                                <button type="button" @click="amount_paid = 100" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300">$100</button>
                                <button type="button" @click="amount_paid = 500" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300">$500</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Cash Given by Customer ($) *</label>
                                <input type="number" step="0.01" name="amount_paid" x-model.number="amount_paid" min="0" required class="w-full px-4 py-3 rounded-2xl bg-emerald-50/50 border-2 border-emerald-500 text-emerald-900 text-lg font-black focus:outline-none focus:ring-2 focus:ring-emerald-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method</label>
                                <select name="payment_method" class="w-full px-4 py-3 rounded-2xl bg-white border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-emerald-500">
                                    <option value="CASH">💵 Cash (Counter)</option>
                                    <option value="POS_CARD">💳 Card / POS Machine</option>
                                    <option value="BANK_TRANSFER">🏦 Bank / Mobile Transfer</option>
                                    <option value="CHEQUE">📑 Cheque</option>
                                </select>
                            </div>
                        </div>

                        <!-- Extra Options: Tax & Discount -->
                        <div class="pt-3 border-t border-slate-100">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Extra Discount ($)</label>
                                    <input type="number" step="0.01" name="discount_amount" x-model.number="discount_amount" min="0" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-amber-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Tax / VAT ($)</label>
                                    <input type="number" step="0.01" name="tax_amount" x-model.number="tax_amount" min="0" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Big Dark Visual Summary & Change Display -->
                    <div class="lg:col-span-5 p-6 rounded-3xl bg-slate-900 text-white flex flex-col justify-between shadow-xl">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm text-slate-400">
                                <span>Subtotal:</span>
                                <span class="font-mono font-bold text-white text-base" x-text="`$${calculateSubtotal().toFixed(2)}`"></span>
                            </div>

                            <div class="flex justify-between text-xs text-slate-400" x-show="discount_amount > 0">
                                <span>Discount:</span>
                                <span class="font-mono font-bold text-amber-400" x-text="`-$${(discount_amount || 0).toFixed(2)}`"></span>
                            </div>

                            <div class="flex justify-between text-xs text-slate-400" x-show="tax_amount > 0">
                                <span>Tax:</span>
                                <span class="font-mono font-bold text-slate-300" x-text="`+$${(tax_amount || 0).toFixed(2)}`"></span>
                            </div>

                            <div class="pt-3 border-t border-slate-800 flex justify-between items-baseline">
                                <span class="text-sm uppercase tracking-wider font-bold text-slate-300">Total Due:</span>
                                <span class="font-mono text-3xl font-black text-emerald-400" x-text="`$${calculateGrandTotal().toFixed(2)}`"></span>
                            </div>

                            <!-- Change to Return Banner -->
                            <div class="mt-3 p-3.5 rounded-2xl bg-emerald-950/80 border border-emerald-500/50 flex items-center justify-between" x-show="amount_paid > calculateGrandTotal() && calculateGrandTotal() > 0">
                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-emerald-400">Change to Return:</span>
                                    <span class="text-xl font-black text-emerald-300" x-text="`$${(amount_paid - calculateGrandTotal()).toFixed(2)}`"></span>
                                </div>
                                <i data-lucide="arrow-down-left" class="w-6 h-6 text-emerald-400"></i>
                            </div>

                            <!-- Remaining Credit / Debt Banner -->
                            <div class="mt-3 p-3.5 rounded-2xl bg-amber-950/80 border border-amber-500/50 flex items-center justify-between" x-show="amount_paid < calculateGrandTotal() && calculateGrandTotal() > 0">
                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-amber-400">Remaining Credit Due:</span>
                                    <span class="text-xl font-black text-amber-300" x-text="`$${Math.max(0, calculateGrandTotal() - amount_paid).toFixed(2)}`"></span>
                                </div>
                                <i data-lucide="clock" class="w-6 h-6 text-amber-400"></i>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:brightness-110 text-white font-black text-base shadow-xl shadow-emerald-600/30 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                <span>Complete Sale & Print Receipt</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function cashierPos() {
            return {
                catalog: @json($products),
                items: [
                    {
                        product_id: '',
                        quantity: 1,
                        unit_price: 0,
                        line_discount: 0,
                        available_stock: null
                    }
                ],
                tax_amount: 0,
                discount_amount: 0,
                amount_paid: 0,

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_price: 0, line_discount: 0, available_stock: null });
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
                        item.unit_price = parseFloat(prod.selling_price);
                        item.available_stock = parseInt(prod.current_stock);
                        this.setExactCash();
                    }
                },
                calculateSubtotal() {
                    return this.items.reduce((sum, item) => {
                        const line = ((item.quantity || 0) * (item.unit_price || 0)) - (item.line_discount || 0);
                        return sum + Math.max(0, line);
                    }, 0);
                },
                calculateGrandTotal() {
                    return Math.max(0, this.calculateSubtotal() + (this.tax_amount || 0) - (this.discount_amount || 0));
                },
                setExactCash() {
                    this.amount_paid = parseFloat(this.calculateGrandTotal().toFixed(2));
                }
            }
        }
    </script>
</x-layouts.app>
