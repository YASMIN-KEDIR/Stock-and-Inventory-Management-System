<x-layouts.app>
    <x-slot:title>Point of Sale (POS)</x-slot:title>
    <x-slot:headerTitle>Point of Sale</x-slot:headerTitle>
    <x-slot:headerSubtitle>Fast Checkout & Margin Tracker</x-slot:headerSubtitle>

    <div class="max-w-6xl mx-auto" x-data="cashierPos()">
        <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
            @csrf

            <!-- Top Row: Cashier Sale Settings -->
            <div class="glass-card rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs bg-white">
                <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">New Sale Transaction</h2>
                            <p class="text-xs text-slate-500">Sell at custom prices and track your profits in real-time</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500 font-medium">Date:</span>
                        <input type="date" id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="customer_id" class="block text-xs font-bold text-slate-700 mb-1.5">Customer (Optional)</label>
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
                        <label for="notes" class="block text-xs font-bold text-slate-700 mb-1.5">Sale Note / Memo (Optional)</label>
                        <input type="text" id="notes" name="notes" value="{{ old('notes') }}" placeholder="e.g. Paid in cash, Takeaway, Token #4" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm placeholder-slate-400 focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <!-- Main Items Table -->
            <div class="glass-card rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs bg-white">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Items Being Sold</h2>
                        <p class="text-xs text-slate-500">Pick products, set custom selling price, and view profit margins</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="openQuickAddModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200/60 shadow-xs transition-all cursor-pointer">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            <span>+ Quick Add Product</span>
                        </button>
                        <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200/60 shadow-xs transition-all cursor-pointer">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>+ Add Line Item</span>
                        </button>
                    </div>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col md:flex-row items-center gap-4 transition-all">
                            
                            <!-- Product Picker with Cost Insight -->
                            <div class="flex-1 w-full">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-bold text-slate-700">Select Product</label>
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <span class="text-slate-500 font-semibold" x-show="item.available_stock !== null">
                                            Stock: <strong class="text-slate-800" x-text="item.available_stock"></strong>
                                        </span>
                                        <span class="text-indigo-600 font-bold bg-indigo-50 px-1.5 py-0.5 rounded" x-show="item.cost_price > 0">
                                            Cost: $<span x-text="(item.cost_price || 0).toFixed(2)"></span>
                                        </span>
                                    </div>
                                </div>
                                <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="onProductSelect(item)" required class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-emerald-500">
                                    <option value="">-- Choose Product to Sell --</option>
                                    <template x-for="p in catalog" :key="p.id">
                                        <option :value="p.id" x-text="`${p.name} — (Cost: $${parseFloat(p.cost_price).toFixed(2)}, In-Stock: ${p.current_stock})`"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Quantity with +/- Stepper -->
                            <div class="w-full md:w-36">
                                <label class="block text-xs font-bold text-slate-600 mb-1 text-center">Quantity</label>
                                <div class="flex items-center rounded-xl bg-white border border-slate-300 overflow-hidden shadow-xs">
                                    <button type="button" @click="item.quantity = Math.max(1, (parseInt(item.quantity) || 1) - 1); syncPayment();" class="px-3 py-2 text-slate-600 hover:bg-slate-100 font-black text-base">-</button>
                                    <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="syncPayment()" @focus="$event.target.select()" min="1" placeholder="1" required class="w-full py-2 text-center text-sm font-black text-slate-900 border-x border-slate-200 focus:outline-none">
                                    <button type="button" @click="item.quantity = (parseInt(item.quantity) || 1) + 1; syncPayment();" class="px-3 py-2 text-slate-600 hover:bg-slate-100 font-black text-base">+</button>
                                </div>
                            </div>

                            <!-- Selling Price (Dynamic / Variable Per Sale) -->
                            <div class="w-full md:w-40">
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-bold text-slate-800">Selling Price ($)</label>
                                    <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-1 rounded">Variable</span>
                                </div>
                                <input type="number" step="0.01" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="syncPayment()" @focus="$event.target.select()" min="0" placeholder="0.00" required class="w-full px-3 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-black text-slate-900 focus:ring-2 focus:ring-emerald-500">
                                
                                <!-- Real-time Line Profit Indicator -->
                                <div class="mt-1 flex justify-between items-center text-[10px]" x-show="item.product_id">
                                    <span class="text-slate-400">Profit/unit:</span>
                                    <span :class="((item.unit_price || 0) - (item.cost_price || 0)) >= 0 ? 'text-emerald-700 font-bold' : 'text-rose-600 font-bold'" x-text="`$${((item.unit_price || 0) - (item.cost_price || 0)).toFixed(2)}`"></span>
                                </div>
                            </div>

                            <!-- Line Discount -->
                            <div class="w-full md:w-28">
                                <label class="block text-xs font-bold text-slate-600 mb-1">Discount ($)</label>
                                <input type="number" step="0.01" :name="`items[${index}][line_discount]`" x-model.number="item.line_discount" @input="syncPayment()" @focus="$event.target.select()" min="0" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-sm text-amber-700 font-semibold focus:ring-2 focus:ring-emerald-500">
                            </div>

                            <!-- Line Total -->
                            <div class="w-full md:w-32 text-right">
                                <label class="block text-xs font-bold text-slate-500 mb-1">Subtotal</label>
                                <span class="text-base font-black text-slate-900 block py-1.5" x-text="`$${Math.max(0, (((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)) - (parseFloat(item.line_discount) || 0))).toFixed(2)}`"></span>
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
            </div>

            <!-- Bottom Checkout & Cashier Settlement Card -->
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-lg bg-white">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- Left: Cashier Payment Entry -->
                    <div class="lg:col-span-7 space-y-5">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <i data-lucide="wallet" class="w-5 h-5 text-emerald-600"></i>
                            <span>Payment & Cash Collection</span>
                        </h3>

                        <!-- Quick Cash Tendered Shortcuts -->
                        <div>
                            <span class="block text-xs font-bold text-slate-600 mb-2">Quick Cash Shortcuts:</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="setExactCash()" class="px-3 py-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-bold border border-emerald-300 cursor-pointer">
                                    ⚡ Exact Total ($<span x-text="calculateGrandTotal().toFixed(2)"></span>)
                                </button>
                                <button type="button" @click="amount_paid = 10; userManuallySetPayment = true;" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 cursor-pointer">$10</button>
                                <button type="button" @click="amount_paid = 20; userManuallySetPayment = true;" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 cursor-pointer">$20</button>
                                <button type="button" @click="amount_paid = 50; userManuallySetPayment = true;" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 cursor-pointer">$50</button>
                                <button type="button" @click="amount_paid = 100; userManuallySetPayment = true;" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 cursor-pointer">$100</button>
                                <button type="button" @click="amount_paid = 500; userManuallySetPayment = true;" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 cursor-pointer">$500</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Amount Paid by Customer ($)</label>
                                <input type="number" step="0.01" name="amount_paid" x-model.number="amount_paid" @input="userManuallySetPayment = true" @focus="$event.target.select()" min="0" placeholder="0.00" class="w-full px-4 py-3 rounded-2xl bg-emerald-50/50 border-2 border-emerald-500 text-emerald-900 text-lg font-black focus:outline-none focus:ring-2 focus:ring-emerald-600">
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
                                    <input type="number" step="0.01" name="discount_amount" x-model.number="discount_amount" @input="syncPayment()" @focus="$event.target.select()" min="0" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-amber-700">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Tax / VAT ($)</label>
                                    <input type="number" step="0.01" name="tax_amount" x-model.number="tax_amount" @input="syncPayment()" @focus="$event.target.select()" min="0" placeholder="0.00" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Big Dark Visual Summary, Margin Tracker & Checkout -->
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

                            <!-- Live Gross Profit Badge -->
                            <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-between">
                                <div>
                                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Estimated Profit:</span>
                                    <span class="text-base font-black text-emerald-400" x-text="`+$${calculateTotalProfit().toFixed(2)}`"></span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider">Buying Cost:</span>
                                    <span class="text-xs font-bold text-slate-300" x-text="`$${calculateTotalCost().toFixed(2)}`"></span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-800 flex justify-between items-baseline">
                                <span class="text-sm uppercase tracking-wider font-bold text-slate-300">Total Due:</span>
                                <span class="font-mono text-3xl font-black text-emerald-400" x-text="`$${calculateGrandTotal().toFixed(2)}`"></span>
                            </div>

                            <!-- Change to Return Banner -->
                            <div class="mt-2 p-3 rounded-2xl bg-emerald-950/80 border border-emerald-500/50 flex items-center justify-between" x-show="amount_paid > calculateGrandTotal() && calculateGrandTotal() > 0">
                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-emerald-400">Change to Return:</span>
                                    <span class="text-xl font-black text-emerald-300" x-text="`$${(amount_paid - calculateGrandTotal()).toFixed(2)}`"></span>
                                </div>
                                <i data-lucide="arrow-down-left" class="w-6 h-6 text-emerald-400"></i>
                            </div>

                            <!-- Remaining Credit / Debt Banner -->
                            <div class="mt-2 p-3 rounded-2xl bg-amber-950/80 border border-amber-500/50 flex items-center justify-between" x-show="amount_paid < calculateGrandTotal() && calculateGrandTotal() > 0">
                                <div>
                                    <span class="block text-[11px] font-bold uppercase tracking-wider text-amber-400">Remaining Credit Due:</span>
                                    <span class="text-xl font-black text-amber-300" x-text="`$${Math.max(0, calculateGrandTotal() - amount_paid).toFixed(2)}`"></span>
                                </div>
                                <i data-lucide="clock" class="w-6 h-6 text-amber-400"></i>
                            </div>
                        </div>

                        <div class="pt-5">
                            <button type="submit" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:brightness-110 text-white font-black text-base shadow-xl shadow-emerald-600/30 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                <span>Complete Sale & Print Receipt</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Quick Add Product Modal -->
        <div x-show="showQuickAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showQuickAddModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Quick Add Product</h3>
                    </div>
                    <button type="button" @click="showQuickAddModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Error Alert Box -->
                <div x-show="quickModalError" x-cloak class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700">
                    <p x-text="quickModalError"></p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Product Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" x-ref="quickNameInput" x-model="newProduct.name" @keydown.enter.prevent="submitQuickProduct()" placeholder="e.g. Wireless Mouse, Red Shirt, Coffee" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Buying Cost ($)</label>
                            <input type="number" step="0.01" x-model="newProduct.cost_price" @keydown.enter.prevent="submitQuickProduct()" @focus="$event.target.select()" placeholder="0.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Selling Price ($)</label>
                            <input type="number" step="0.01" x-model="newProduct.selling_price" @keydown.enter.prevent="submitQuickProduct()" @focus="$event.target.select()" placeholder="0.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-sm font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Opening Stock Qty</label>
                        <input type="number" x-model="newProduct.current_stock" @keydown.enter.prevent="submitQuickProduct()" @focus="$event.target.select()" placeholder="0" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showQuickAddModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold cursor-pointer">Cancel</button>
                    <button type="button" @click="submitQuickProduct()" :disabled="savingProduct" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/20 flex items-center gap-1.5 cursor-pointer">
                        <span x-show="!savingProduct">Save & Select</span>
                        <span x-show="savingProduct">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cashierPos() {
            return {
                catalog: @json($products),
                showQuickAddModal: false,
                savingProduct: false,
                quickModalError: '',
                newProduct: {
                    name: '',
                    cost_price: '',
                    selling_price: '',
                    current_stock: ''
                },
                items: [
                    {
                        product_id: '',
                        quantity: 1,
                        unit_price: '',
                        cost_price: 0,
                        line_discount: '',
                        available_stock: null
                    }
                ],
                tax_amount: '',
                discount_amount: '',
                amount_paid: '',
                userManuallySetPayment: false,

                openQuickAddModal() {
                    this.quickModalError = '';
                    this.newProduct = { name: '', cost_price: '', selling_price: '', current_stock: '' };
                    this.showQuickAddModal = true;
                    this.$nextTick(() => {
                        if (this.$refs.quickNameInput) {
                            this.$refs.quickNameInput.focus();
                        }
                    });
                },

                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_price: '', cost_price: 0, line_discount: '', available_stock: null });
                    this.$nextTick(() => lucide.createIcons());
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.syncPayment();
                    }
                },
                onProductSelect(item) {
                    const prod = this.catalog.find(p => p.id == item.product_id);
                    if (prod) {
                        item.unit_price = prod.selling_price !== null && prod.selling_price !== undefined ? parseFloat(prod.selling_price) : '';
                        item.cost_price = prod.cost_price !== null && prod.cost_price !== undefined ? parseFloat(prod.cost_price) : 0;
                        item.available_stock = parseInt(prod.current_stock || 0);
                        this.syncPayment();
                    }
                },
                calculateSubtotal() {
                    return this.items.reduce((sum, item) => {
                        const q = parseFloat(item.quantity) || 0;
                        const p = parseFloat(item.unit_price) || 0;
                        const d = parseFloat(item.line_discount) || 0;
                        const line = (q * p) - d;
                        return sum + Math.max(0, line);
                    }, 0);
                },
                calculateTotalCost() {
                    return this.items.reduce((sum, item) => {
                        const q = parseFloat(item.quantity) || 0;
                        const c = parseFloat(item.cost_price) || 0;
                        return sum + (q * c);
                    }, 0);
                },
                calculateGrandTotal() {
                    const sub = this.calculateSubtotal();
                    const tax = parseFloat(this.tax_amount) || 0;
                    const disc = parseFloat(this.discount_amount) || 0;
                    return Math.max(0, sub + tax - disc);
                },
                calculateTotalProfit() {
                    const rev = this.calculateGrandTotal();
                    const cost = this.calculateTotalCost();
                    return Math.max(0, rev - cost);
                },
                syncPayment() {
                    if (!this.userManuallySetPayment) {
                        const grand = this.calculateGrandTotal();
                        this.amount_paid = grand > 0 ? parseFloat(grand.toFixed(2)) : '';
                    }
                },
                setExactCash() {
                    this.userManuallySetPayment = false;
                    const grand = this.calculateGrandTotal();
                    this.amount_paid = grand > 0 ? parseFloat(grand.toFixed(2)) : '';
                },
                async submitQuickProduct() {
                    this.quickModalError = '';
                    const name = (this.newProduct.name || '').trim();
                    if (!name) {
                        this.quickModalError = 'Please enter a Product Name.';
                        if (this.$refs.quickNameInput) this.$refs.quickNameInput.focus();
                        return;
                    }

                    this.savingProduct = true;

                    try {
                        const cost = parseFloat(this.newProduct.cost_price) || 0;
                        const price = parseFloat(this.newProduct.selling_price) || 0;
                        const stock = parseInt(this.newProduct.current_stock) || 0;

                        const response = await fetch("{{ route('products.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                name: name,
                                cost_price: cost,
                                selling_price: price,
                                current_stock: stock
                            })
                        });

                        const data = await response.json();

                        if (response.ok && (data.success || data.product)) {
                            const newProd = data.product || data;
                            this.catalog.push(newProd);

                            // Select this product in the current or first available line item
                            let targetItem = this.items.find(i => !i.product_id);
                            if (!targetItem) {
                                targetItem = { product_id: '', quantity: 1, unit_price: '', cost_price: 0, line_discount: '', available_stock: null };
                                this.items.push(targetItem);
                            }
                            targetItem.product_id = newProd.id;
                            targetItem.unit_price = parseFloat(newProd.selling_price || 0);
                            targetItem.cost_price = parseFloat(newProd.cost_price || 0);
                            targetItem.available_stock = parseInt(newProd.current_stock || 0);

                            this.showQuickAddModal = false;
                            this.newProduct = { name: '', cost_price: '', selling_price: '', current_stock: '' };
                            this.syncPayment();
                            this.$nextTick(() => lucide.createIcons());
                        } else {
                            if (data.errors) {
                                const msgs = Object.values(data.errors).flat().join(' ');
                                this.quickModalError = msgs || data.message || 'Validation failed.';
                            } else {
                                this.quickModalError = data.message || 'Could not save product. Please try again.';
                            }
                        }
                    } catch (e) {
                        console.error(e);
                        this.quickModalError = 'Network or server error while creating product.';
                    } finally {
                        this.savingProduct = false;
                    }
                }
            }
        }
    </script>
</x-layouts.app>
