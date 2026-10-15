<x-layouts.app>
    <x-slot:title>Point of Sale (POS)</x-slot:title>
    <x-slot:headerTitle>Point of Sale (POS)</x-slot:headerTitle>
    <x-slot:headerSubtitle>Sell Products & Checkout</x-slot:headerSubtitle>

    <div class="max-w-6xl mx-auto" x-data="salesCheckout()">
        <form method="POST" action="{{ route('sales.store') }}" enctype="multipart/form-data" @submit="handleSubmit($event)" class="space-y-6">
            @csrf

            <!-- ========================================================================= -->
            <!-- STEP 1: SELECT PRODUCT, QUANTITY (PIECE OR PACK), & PRICE -->
            <!-- ========================================================================= -->
            <div class="glass-card rounded-3xl p-5 sm:p-6 border-2 border-slate-200 shadow-xs bg-white space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-200">
                                Step 1 &bull; Items to Sell
                            </span>
                            <h2 class="text-base font-black text-slate-900 mt-0.5">Select Products, Quantity & Selling Price</h2>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="openQuickAddModal()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition-all cursor-pointer">
                            <i data-lucide="package-plus" class="w-4 h-4 text-emerald-600"></i>
                            <span>+ Quick Register Product</span>
                        </button>
                        <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>+ Add Another Product</span>
                        </button>
                    </div>
                </div>

                <!-- Product Line Items List -->
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/90 border-2 border-slate-200 hover:border-slate-300 transition-all space-y-4 relative" :style="{ zIndex: 50 - index }">
                            
                            <!-- Header Row: Item Number & Remove Button -->
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                                <span class="text-xs font-black uppercase text-slate-500 tracking-wider" x-text="`Product #${index + 1}`"></span>
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1 cursor-pointer">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Remove</span>
                                </button>
                            </div>

                            <!-- Row 1: Search / Select Product -->
                            <div class="relative z-30" @click.away="item.isDropdownOpen = false">
                                <div class="flex flex-wrap justify-between items-center gap-2 mb-1.5">
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-900">
                                        1. Click to Choose or Type to Search Product <span class="text-rose-600">*</span>
                                    </label>
                                    <div class="flex items-center gap-2 text-xs" x-show="item.product_id">
                                        <span class="inline-flex items-center gap-1 text-emerald-800 font-bold bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-lg">
                                            ✓ Selected
                                        </span>
                                        <span class="text-slate-600 font-bold" x-show="item.available_stock !== null">
                                            In Stock: <strong class="text-slate-900" x-text="`${item.available_stock} Units`"></strong>
                                        </span>
                                        <span class="text-indigo-800 font-black bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-lg" x-show="item.cost_price > 0">
                                            Buying Cost: <span x-text="formatCurrency(item.cost_price)"></span> Birr
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs" x-show="!item.product_id && item.searchQuery">
                                        <span class="text-amber-700 font-bold bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-lg">
                                            Click product below to select
                                        </span>
                                    </div>
                                </div>

                                <!-- Search Input Field with Icons -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i data-lucide="search" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text"
                                           x-model="item.searchQuery"
                                           @focus="item.isDropdownOpen = true"
                                           @click="item.isDropdownOpen = true"
                                           @input="item.isDropdownOpen = true; if(item.product_id && item.searchQuery !== getProductName(item.product_id)) { item.product_id = ''; }"
                                           @keydown.escape="item.isDropdownOpen = false"
                                           @keydown.enter.prevent="selectFirstMatch(item)"
                                           placeholder="Type product name, barcode, or click here to choose..."
                                           autocomplete="off"
                                           class="w-full pl-10 pr-20 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100 transition-all">
                                    
                                    <!-- Clear & Toggle Buttons -->
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                                        <button type="button" 
                                                x-show="item.product_id || item.searchQuery" 
                                                @click="clearProductSelection(item)" 
                                                class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 cursor-pointer" 
                                                title="Clear selection">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                        <button type="button" 
                                                @click="item.isDropdownOpen = !item.isDropdownOpen" 
                                                class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 cursor-pointer"
                                                title="Toggle product list">
                                            <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200" :class="item.isDropdownOpen ? 'rotate-180' : ''"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Hidden input storing actual product_id for backend -->
                                <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id" required>

                                <!-- Floating Filtered Dropdown Results List -->
                                <div x-show="item.isDropdownOpen" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute z-50 left-0 right-0 mt-1 max-h-64 overflow-y-auto rounded-2xl bg-white border-2 border-slate-300 shadow-2xl divide-y divide-slate-100">
                                    
                                    <div class="px-3.5 py-2 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-[11px] font-bold text-slate-500">
                                        <span>Click product to select:</span>
                                        <span x-text="`${getFilteredCatalog(item).length} product(s) found`"></span>
                                    </div>

                                    <template x-for="p in getFilteredCatalog(item)" :key="p.id">
                                        <button type="button"
                                                @click="selectProduct(item, p)"
                                                class="w-full px-4 py-3 text-left hover:bg-indigo-50/80 transition-colors flex items-center justify-between cursor-pointer group"
                                                :class="item.product_id == p.id ? 'bg-indigo-50/70 font-black' : ''">
                                            <div>
                                                <div class="text-sm font-bold text-slate-900 group-hover:text-indigo-700 flex items-center gap-2">
                                                    <span x-text="p.name"></span>
                                                    <span x-show="item.product_id == p.id" class="text-[11px] text-indigo-700 font-black bg-indigo-100 px-2 py-0.5 rounded-md">✓ Selected</span>
                                                </div>
                                                <div class="text-xs text-slate-500 font-mono mt-0.5" x-show="p.barcode">
                                                    <span x-text="`Barcode: ${p.barcode}`"></span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs font-black block" :class="p.current_stock <= 0 ? 'text-rose-600' : (p.current_stock <= 5 ? 'text-amber-600' : 'text-emerald-700')" x-text="`${p.current_stock} in stock`"></span>
                                                <span class="text-[11px] font-bold text-slate-500" x-show="p.cost_price > 0" x-text="`Cost: ${formatCurrency(p.cost_price)} Birr`"></span>
                                            </div>
                                        </button>
                                    </template>

                                    <!-- No Results Found Fallback -->
                                    <div x-show="getFilteredCatalog(item).length === 0" class="p-4 text-center text-xs text-slate-600 font-bold space-y-2">
                                        <p>No products found matching "<span class="text-slate-900 font-black" x-text="item.searchQuery"></span>".</p>
                                        <button type="button" @click="openQuickAddModal(item.searchQuery)" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold cursor-pointer transition-colors">
                                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                            <span>Register "<span x-text="item.searchQuery"></span>" as new product</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Quantity Mode (Single Piece vs In Pack / Dozen) -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start pt-2">
                                
                                <!-- Quantity Section (50% on desktop, 100% on mobile) -->
                                <div class="w-full p-4 sm:p-5 rounded-2xl bg-white border-2 border-slate-200 space-y-3 shadow-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800">
                                            2. Quantity Choice:
                                        </span>
                                        <!-- Packaging Toggle: Single vs In Pack -->
                                        <div class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200">
                                            <button type="button" @click="item.mode = 'single'; onModeChange(item)" :class="item.mode === 'single' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2.5 py-1 rounded-lg text-xs font-black transition-all cursor-pointer">
                                                Single Piece
                                            </button>
                                            <button type="button" @click="item.mode = 'pack'; onModeChange(item)" :class="item.mode === 'pack' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2.5 py-1 rounded-lg text-xs font-black transition-all cursor-pointer">
                                                📦 In Pack / Dozen
                                            </button>
                                        </div>
                                    </div>

                                    <!-- If Single Piece Mode -->
                                    <div x-show="item.mode === 'single'" class="space-y-1">
                                        <label class="block text-[11px] font-bold text-slate-600">Enter Number of Pieces:</label>
                                        <div class="flex items-center rounded-xl bg-slate-50 border-2 border-slate-300 overflow-hidden">
                                            <button type="button" @click="item.singleQty = Math.max(1, (parseInt(item.singleQty) || 1) - 1); computeItem(item);" class="px-3.5 py-2 text-slate-700 hover:bg-slate-200 font-black text-base cursor-pointer">-</button>
                                            <input type="number" x-model.number="item.singleQty" @input="computeItem(item)" @focus="$event.target.select()" min="1" placeholder="1" class="w-full py-2 text-center text-sm font-black text-slate-900 border-x border-slate-200 bg-white focus:outline-none">
                                            <button type="button" @click="item.singleQty = (parseInt(item.singleQty) || 1) + 1; computeItem(item);" class="px-3.5 py-2 text-slate-700 hover:bg-slate-200 font-black text-base cursor-pointer">+</button>
                                        </div>
                                        <span class="text-[11px] text-slate-500 font-semibold block">Total pieces sold: <strong class="text-slate-900" x-text="item.quantity"></strong></span>
                                    </div>

                                    <!-- If In Pack / Dozen Mode -->
                                    <div x-show="item.mode === 'pack'" class="space-y-2.5">
                                        <div class="grid grid-cols-2 gap-2">
                                            <!-- Pack Type -->
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pack Type:</label>
                                                <select x-model="item.packType" @change="onPackTypeChange(item)" class="w-full px-2.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs font-bold text-slate-900 focus:outline-none">
                                                    <option value="dozen">Dozen (12 pcs)</option>
                                                    <option value="box">Carton / Box</option>
                                                    <option value="pack">Pack / Bundle</option>
                                                </select>
                                            </div>

                                            <!-- Pieces per Pack -->
                                            <div>
                                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pieces in 1 Pack:</label>
                                                <input type="number" x-model.number="item.pcsPerPack" @input="computeItem(item)" :disabled="item.packType === 'dozen'" min="1" class="w-full px-2.5 py-2 rounded-xl bg-slate-50 border border-slate-300 text-xs font-bold text-slate-900 focus:outline-none disabled:bg-slate-100 disabled:text-slate-500">
                                            </div>
                                        </div>

                                        <!-- Number of Packs/Dozens -->
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 mb-1">How Many Packs / Dozens:</label>
                                            <div class="flex items-center rounded-xl bg-slate-50 border-2 border-slate-300 overflow-hidden">
                                                <button type="button" @click="item.packCount = Math.max(1, (parseInt(item.packCount) || 1) - 1); computeItem(item);" class="px-3.5 py-2 text-slate-700 hover:bg-slate-200 font-black text-base cursor-pointer">-</button>
                                                <input type="number" x-model.number="item.packCount" @input="computeItem(item)" @focus="$event.target.select()" min="1" placeholder="1" class="w-full py-2 text-center text-sm font-black text-slate-900 border-x border-slate-200 bg-white focus:outline-none">
                                                <button type="button" @click="item.packCount = (parseInt(item.packCount) || 1) + 1; computeItem(item);" class="px-3.5 py-2 text-slate-700 hover:bg-slate-200 font-black text-base cursor-pointer">+</button>
                                            </div>
                                        </div>

                                        <div class="p-2 rounded-lg bg-indigo-50/70 border border-indigo-200 text-xs text-indigo-900 font-bold flex items-center justify-between">
                                            <span>Total Pieces from Stock:</span>
                                            <span class="text-sm font-black text-indigo-700" x-text="`${item.packCount || 1} packs &times; ${item.pcsPerPack} = ${item.quantity} pieces`"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selling Price Section (50% on desktop, 100% on mobile) -->
                                <div class="w-full p-4 sm:p-5 rounded-2xl bg-white border-2 border-slate-200 space-y-3 shadow-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-800">
                                            3. Price Settings:
                                        </span>

                                        <!-- If in Pack Mode: Ask to price per Pack or per Piece -->
                                        <div x-show="item.mode === 'pack'" class="inline-flex rounded-xl bg-slate-100 p-1 border border-slate-200">
                                            <button type="button" @click="item.pricingChoice = 'pack'; onPricingChoiceChange(item)" :class="item.pricingChoice === 'pack' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2 py-0.5 rounded-lg text-xs font-black transition-all cursor-pointer">
                                                Price per Pack
                                            </button>
                                            <button type="button" @click="item.pricingChoice = 'piece'; onPricingChoiceChange(item)" :class="item.pricingChoice === 'piece' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="px-2 py-0.5 rounded-lg text-xs font-black transition-all cursor-pointer">
                                                Price per Piece
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Price in Single Mode -->
                                    <div x-show="item.mode === 'single'" class="space-y-1">
                                        <label class="block text-[11px] font-bold text-slate-600">
                                            Selling Price per Piece (Birr) <span class="text-rose-600">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3.5 top-2.5 text-xs font-black text-slate-400">Birr</span>
                                            <input type="number" step="0.01" x-model.number="item.unit_price" @input="computeItem(item)" @focus="$event.target.select()" min="0" placeholder="0.00" required class="w-full pl-12 pr-3 py-2 rounded-xl bg-white border-2 border-emerald-500 text-sm font-black text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                        </div>
                                    </div>

                                    <!-- Price in Pack Mode: Price per Pack -->
                                    <div x-show="item.mode === 'pack' && item.pricingChoice === 'pack'" class="space-y-1.5">
                                        <label class="block text-[11px] font-bold text-slate-600">
                                            Selling Price per Pack / Dozen (Birr) <span class="text-rose-600">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3.5 top-2.5 text-xs font-black text-slate-400">Birr</span>
                                            <input type="number" step="0.01" x-model.number="item.packPrice" @input="onPackPriceInput(item)" @focus="$event.target.select()" min="0" placeholder="0.00" class="w-full pl-12 pr-3 py-2 rounded-xl bg-white border-2 border-emerald-500 text-sm font-black text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                        </div>
                                        <div class="text-[11px] text-slate-500 flex justify-between">
                                            <span>Calculated per piece:</span>
                                            <strong class="text-slate-900" x-text="`${formatCurrency(item.unit_price)} Birr / piece`"></strong>
                                        </div>
                                    </div>

                                    <!-- Price in Pack Mode: Price per Piece -->
                                    <div x-show="item.mode === 'pack' && item.pricingChoice === 'piece'" class="space-y-1.5">
                                        <label class="block text-[11px] font-bold text-slate-600">
                                            Selling Price per Piece (Birr) <span class="text-rose-600">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3.5 top-2.5 text-xs font-black text-slate-400">Birr</span>
                                            <input type="number" step="0.01" x-model.number="item.unit_price" @input="onPiecePriceInput(item)" @focus="$event.target.select()" min="0" placeholder="0.00" class="w-full pl-12 pr-3 py-2 rounded-xl bg-white border-2 border-emerald-500 text-sm font-black text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                        </div>
                                        <div class="text-[11px] text-slate-500 flex justify-between">
                                            <span>Calculated per pack / dozen:</span>
                                            <strong class="text-slate-900" x-text="`${formatCurrency(item.packPrice)} Birr / pack`"></strong>
                                        </div>
                                    </div>

                                    <!-- Item Subtotal & Profit Banner -->
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                        <div class="text-xs">
                                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Line Total:</span>
                                            <span class="font-black text-base text-slate-900" x-text="`${formatCurrency(getItemSubtotal(item))} Birr`"></span>
                                        </div>
                                        <div class="text-right text-xs" x-show="item.cost_price > 0">
                                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Profit:</span>
                                            <span class="font-bold text-emerald-600" x-text="`+${formatCurrency(getItemProfit(item))} Birr`"></span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Hidden Form Inputs for Backend Submission -->
                            <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
                            <input type="hidden" :name="`items[${index}][unit_price]`" :value="item.unit_price">
                            <input type="hidden" :name="`items[${index}][line_discount]`" value="0">
                        </div>
                    </template>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- STEP 2: PAYMENT METHOD (CASH OR CREDIT) -->
            <!-- ========================================================================= -->
            <div class="glass-card rounded-3xl p-5 sm:p-7 border-2 border-slate-200 shadow-lg bg-white space-y-6">
                
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                        <i data-lucide="wallet" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200">
                            Step 2 &bull; Payment
                        </span>
                        <h2 class="text-base font-black text-slate-900 mt-0.5">Payment Method: Cash or Credit</h2>
                    </div>
                </div>

                <!-- Big Choice Switch: CASH vs CREDIT -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Option 1: Cash / Immediate Payment -->
                    <label @click="paymentType = 'cash'; onPaymentTypeChange()" :class="paymentType === 'cash' ? 'border-emerald-600 bg-emerald-50/70 ring-4 ring-emerald-100' : 'border-slate-200 bg-white hover:border-slate-300'" class="p-4 rounded-2xl border-2 flex items-center gap-3.5 cursor-pointer transition-all">
                        <input type="radio" name="_payment_type_selector" value="cash" x-model="paymentType" class="sr-only">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shrink-0">
                            <i data-lucide="banknote" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-black text-slate-900">Cash / Paid Now</span>
                            <span class="text-xs text-slate-500 font-semibold">Immediate payment (Cash, CBE, Telebirr, Abyssinia)</span>
                        </div>
                    </label>

                    <!-- Option 2: Credit / Debt -->
                    <label @click="paymentType = 'credit'; onPaymentTypeChange()" :class="paymentType === 'credit' ? 'border-amber-500 bg-amber-50/70 ring-4 ring-amber-100' : 'border-slate-200 bg-white hover:border-slate-300'" class="p-4 rounded-2xl border-2 flex items-center gap-3.5 cursor-pointer transition-all">
                        <input type="radio" name="_payment_type_selector" value="credit" x-model="paymentType" class="sr-only">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold shrink-0">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-black text-slate-900">Credit (Debt / ብድር)</span>
                            <span class="text-xs text-slate-500 font-semibold">Customer owes payment (Full credit or Partial credit)</span>
                        </div>
                    </label>
                </div>

                <!-- ========================================================================= -->
                <!-- IF CASH PAYMENT IS CHOSEN -->
                <!-- ========================================================================= -->
                <div x-show="paymentType === 'cash'" class="p-5 rounded-2xl bg-slate-50 border-2 border-slate-200 space-y-5">
                    
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-slate-900 mb-2">
                            Select Payment Channel:
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            
                            <!-- 1. Cash -->
                            <label @click="payment_method = 'CASH'" :class="payment_method === 'CASH' ? 'border-emerald-600 bg-emerald-50 text-emerald-950 font-black shadow-xs' : 'border-slate-300 bg-white text-slate-800 font-bold hover:bg-slate-50'" class="p-3.5 rounded-xl border-2 flex flex-col items-center justify-center text-center gap-1.5 cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="CASH" x-model="payment_method" class="sr-only">
                                <span class="text-xl">💵</span>
                                <span class="text-xs">Cash in Hand</span>
                            </label>

                            <!-- 2. CBE -->
                            <label @click="payment_method = 'CBE'" :class="payment_method === 'CBE' ? 'border-indigo-600 bg-indigo-50 text-indigo-950 font-black shadow-xs' : 'border-slate-300 bg-white text-slate-800 font-bold hover:bg-slate-50'" class="p-3.5 rounded-xl border-2 flex flex-col items-center justify-center text-center gap-1.5 cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="CBE" x-model="payment_method" class="sr-only">
                                <span class="text-xl">🏦</span>
                                <span class="text-xs">CBE Bank</span>
                            </label>

                            <!-- 3. Telebirr -->
                            <label @click="payment_method = 'TELEBIRR'" :class="payment_method === 'TELEBIRR' ? 'border-amber-500 bg-amber-50 text-amber-950 font-black shadow-xs' : 'border-slate-300 bg-white text-slate-800 font-bold hover:bg-slate-50'" class="p-3.5 rounded-xl border-2 flex flex-col items-center justify-center text-center gap-1.5 cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="TELEBIRR" x-model="payment_method" class="sr-only">
                                <span class="text-xl">📱</span>
                                <span class="text-xs">Telebirr</span>
                            </label>

                            <!-- 4. Abyssinia -->
                            <label @click="payment_method = 'ABYSSINIA'" :class="payment_method === 'ABYSSINIA' ? 'border-yellow-600 bg-yellow-50 text-yellow-950 font-black shadow-xs' : 'border-slate-300 bg-white text-slate-800 font-bold hover:bg-slate-50'" class="p-3.5 rounded-xl border-2 flex flex-col items-center justify-center text-center gap-1.5 cursor-pointer transition-all">
                                <input type="radio" name="payment_method" value="ABYSSINIA" x-model="payment_method" class="sr-only">
                                <span class="text-xl">🏛️</span>
                                <span class="text-xs">Bank of Abyssinia</span>
                            </label>
                        </div>
                    </div>

                    <!-- Optional Screenshot Capture & Reference Number -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-200">
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                Capture / Attach Screenshot (Optional)
                            </label>
                            <div class="flex items-center gap-3">
                                <input type="file" name="receipt_screenshot" accept="image/*" @change="handleScreenshotUpload($event)" class="w-full text-xs font-semibold text-slate-700 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-800 hover:file:bg-indigo-100 cursor-pointer">
                            </div>
                            <span class="text-[11px] text-slate-500 font-semibold mt-1 block">Telebirr SMS screenshot, CBE slip, or Abyssinia receipt</span>

                            <!-- Screenshot Preview Thumbnail -->
                            <div x-show="screenshotPreview" class="mt-2 relative inline-block">
                                <img :src="screenshotPreview" alt="Receipt preview" class="w-20 h-20 object-cover rounded-xl border-2 border-indigo-300 shadow-xs">
                                <button type="button" @click="removeScreenshot()" class="absolute -top-1.5 -right-1.5 bg-rose-600 text-white rounded-full p-0.5 hover:bg-rose-700 cursor-pointer">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                Transaction / Reference Number (Optional)
                            </label>
                            <input type="text" name="reference_number" placeholder="e.g. CBE TXN #, Telebirr Trans ID" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600">
                            <span class="text-[11px] text-slate-500 font-semibold mt-1 block">Bank slip number or SMS transfer code</span>
                        </div>
                    </div>

                    <!-- Customer Dropdown (Optional for Cash) -->
                    <div class="pt-2 border-t border-slate-200">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-xs font-black uppercase tracking-wider text-slate-800">
                                Customer Name (Optional for Cash)
                            </label>
                            <button type="button" @click="openQuickCustomerModal()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline cursor-pointer">
                                + Register New Customer
                            </button>
                        </div>
                        <select name="customer_id" x-model="customer_id" @change="onCustomerSelectChange()" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                            <option value="">👤 Walk-In Customer (Cash Sale)</option>
                            <option value="__NEW__" class="font-bold text-indigo-700 bg-indigo-50">+ Add New Customer...</option>
                            <template x-for="c in customersList" :key="c.id">
                                <option :value="c.id" x-text="`${c.name} ${c.phone ? '(' + c.phone + ')' : ''}`"></option>
                            </template>
                        </select>
                    </div>

                </div>

                <!-- ========================================================================= -->
                <!-- IF CREDIT PAYMENT IS CHOSEN -->
                <!-- ========================================================================= -->
                <div x-show="paymentType === 'credit'" class="p-5 rounded-2xl bg-amber-50/80 border-2 border-amber-300 space-y-5">
                    
                    <!-- Credit Mode Switch: Full Credit vs Partial Credit -->
                    <div>
                        <label class="block text-xs font-black uppercase tracking-wider text-amber-950 mb-2">
                            Select Credit Type:
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="button" @click="creditOption = 'full'; onCreditOptionChange()" :class="creditOption === 'full' ? 'bg-amber-600 text-white shadow-xs border-amber-600' : 'bg-white text-slate-800 border-amber-300 hover:bg-amber-100/50'" class="p-3.5 rounded-xl border-2 text-center text-xs font-black transition-all cursor-pointer">
                                ⏳ Full Credit (0 Birr Paid Now &bull; 100% Debt)
                            </button>
                            <button type="button" @click="creditOption = 'partial'; onCreditOptionChange()" :class="creditOption === 'partial' ? 'bg-amber-600 text-white shadow-xs border-amber-600' : 'bg-white text-slate-800 border-amber-300 hover:bg-amber-100/50'" class="p-3.5 rounded-xl border-2 text-center text-xs font-black transition-all cursor-pointer">
                                ⚖️ Partial Credit (Pay Some Now &bull; Rest is Debt)
                            </button>
                        </div>
                    </div>

                    <!-- Customer Dropdown (Required for Credit) -->
                    <div class="p-4 rounded-xl bg-white border-2 border-amber-300 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-black uppercase tracking-wider text-slate-900">
                                Who is taking this credit? (Select Customer) <span class="text-rose-600">*</span>
                            </label>
                            <button type="button" @click="openQuickCustomerModal()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline cursor-pointer">
                                + Register New Customer
                            </button>
                        </div>
                        <select name="customer_id" x-model="customer_id" @change="onCustomerSelectChange()" required class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-amber-600 focus:outline-none">
                            <option value="">-- Choose Customer to Record Debt --</option>
                            <option value="__NEW__" class="font-bold text-indigo-700 bg-indigo-50">+ Add New Customer...</option>
                            <template x-for="c in customersList" :key="c.id">
                                <option :value="c.id" x-text="`${c.name} ${c.phone ? '(' + c.phone + ')' : ''} [Existing Debt: ${formatCurrency(c.outstanding_debt || 0)} Birr]`"></option>
                            </template>
                        </select>
                        <p class="text-[11px] font-semibold text-amber-900">
                            A customer profile is required so the system knows whose account owes this debt.
                        </p>
                    </div>

                    <!-- If Partial Credit: How much paid now input -->
                    <div x-show="creditOption === 'partial'" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    How Much Paid Now (Birr) <span class="text-rose-600">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2.5 text-xs font-black text-slate-400">Birr</span>
                                    <input type="number" step="0.01" x-model.number="amount_paid" @input="onPartialPaidInput()" @focus="$event.target.select()" min="0" placeholder="0.00" class="w-full pl-12 pr-4 py-2.5 rounded-xl bg-white border-2 border-emerald-500 text-base font-black text-slate-900 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Paid Using Channel:
                                </label>
                                <select name="payment_method" x-model="payment_method" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none">
                                    <option value="CASH">💵 Cash in Hand</option>
                                    <option value="CBE">🏦 CBE Bank</option>
                                    <option value="TELEBIRR">📱 Telebirr</option>
                                    <option value="ABYSSINIA">🏛️ Bank of Abyssinia</option>
                                </select>
                            </div>
                        </div>

                        <!-- Optional Screenshot for Partial Payment -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Receipt Screenshot (Optional)</label>
                                <input type="file" name="receipt_screenshot" accept="image/*" class="w-full text-xs font-semibold text-slate-700 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800 hover:file:bg-slate-300 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Txn / Slip # (Optional)</label>
                                <input type="text" name="reference_number" placeholder="Slip or TXN ID" class="w-full px-3 py-2 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-900">
                            </div>
                        </div>
                    </div>

                    <!-- Live Debt Breakdown Cards (How much paid & How much remain) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 text-center">
                            <span class="block text-[10px] font-bold uppercase text-slate-500">Total Bill</span>
                            <span class="text-base font-black text-slate-900" x-text="`${formatCurrency(calculateGrandTotal())} Birr`"></span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-300 text-center">
                            <span class="block text-[10px] font-bold uppercase text-emerald-800">Paid Now</span>
                            <span class="text-base font-black text-emerald-700" x-text="`${formatCurrency(amount_paid || 0)} Birr`"></span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-amber-600 text-white text-center shadow-xs">
                            <span class="block text-[10px] font-bold uppercase text-amber-100">Remaining Debt</span>
                            <span class="text-base font-black text-white" x-text="`${formatCurrency(calculateRemainingCredit())} Birr`"></span>
                        </div>
                    </div>

                    <!-- Customer Ledger Banner if Customer is Selected -->
                    <div x-show="selectedCustomer" class="p-3 rounded-xl bg-white border border-amber-300 flex items-center justify-between text-xs font-bold text-slate-800">
                        <div>
                            <span>Customer: <strong class="text-slate-900" x-text="selectedCustomer ? selectedCustomer.name : ''"></strong></span>
                            <span class="text-slate-500 block text-[11px]" x-text="`Previous Debt: ${formatCurrency(selectedCustomer ? selectedCustomer.outstanding_debt : 0)} Birr`"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-amber-800 font-black" x-text="`New Total Debt: ${formatCurrency((selectedCustomer ? (parseFloat(selectedCustomer.outstanding_debt) || 0) : 0) + calculateRemainingCredit())} Birr`"></span>
                        </div>
                    </div>

                </div>

                <!-- Hidden amount_paid input for form submission -->
                <input type="hidden" name="amount_paid" :value="amount_paid">
                <input type="hidden" name="sale_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="discount_amount" :value="discount_amount || 0">

                <!-- Grand Total & Submit Button -->
                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-500">Total Invoice Amount:</span>
                        <div class="text-3xl font-black text-slate-900 font-mono" x-text="`${formatCurrency(calculateGrandTotal())} Birr`"></div>
                    </div>

                    <button type="submit" class="py-4 px-8 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-base shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <span>Confirm Sale & Save Invoice</span>
                    </button>
                </div>

            </div>
        </form>

        <!-- ========================================================================= -->
        <!-- MODAL 1: Quick Add Customer Modal -->
        <!-- ========================================================================= -->
        <div x-show="showCustomerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showCustomerModal = false" class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-base font-black text-slate-900">+ Register New Customer</h3>
                    </div>
                    <button type="button" @click="showCustomerModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div x-show="customerModalError" x-cloak class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs font-bold text-rose-700">
                    <p x-text="customerModalError"></p>
                </div>

                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                            Customer Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" x-ref="custNameInput" x-model="newCustomer.name" @keydown.enter.prevent="submitQuickCustomer()" placeholder="e.g. Abebe Kebede" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase text-slate-800 mb-1">Phone Number</label>
                        <input type="text" x-model="newCustomer.phone" @keydown.enter.prevent="submitQuickCustomer()" placeholder="e.g. 0911223344" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showCustomerModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-bold cursor-pointer">Cancel</button>
                    <button type="button" @click="submitQuickCustomer()" :disabled="savingCustomer" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-600/20 cursor-pointer">
                        <span x-show="!savingCustomer">Save & Select</span>
                        <span x-show="savingCustomer">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 2: Quick Register Product Modal -->
        <!-- ========================================================================= -->
        <div x-show="showQuickAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="showQuickAddModal = false" class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                            <i data-lucide="package-plus" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-base font-black text-slate-900">+ Quick Register Product</h3>
                    </div>
                    <button type="button" @click="showQuickAddModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div x-show="quickModalError" x-cloak class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs font-bold text-rose-700">
                    <p x-text="quickModalError"></p>
                </div>

                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                            Product Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" x-ref="quickNameInput" x-model="newProduct.name" @keydown.enter.prevent="submitQuickProduct()" placeholder="e.g. Red Shirt, Soap, Oil" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                Buying Cost (Birr) <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" step="0.01" x-model="newProduct.cost_price" @keydown.enter.prevent="submitQuickProduct()" placeholder="0.00" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-black text-slate-900 focus:outline-none focus:border-indigo-600">
                        </div>
                        <div>
                            <label class="block text-xs font-black uppercase text-slate-800 mb-1">
                                Opening Stock
                            </label>
                            <input type="number" x-model="newProduct.current_stock" @keydown.enter.prevent="submitQuickProduct()" placeholder="0" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-black text-slate-900 focus:outline-none focus:border-indigo-600">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="showQuickAddModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-bold cursor-pointer">Cancel</button>
                    <button type="button" @click="submitQuickProduct()" :disabled="savingProduct" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md shadow-emerald-600/20 cursor-pointer">
                        <span x-show="!savingProduct">Save & Select Product</span>
                        <span x-show="savingProduct">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('salesCheckout', salesCheckout);
        });

        function salesCheckout() {
            return {
                catalog: @json($products),
                customersList: @json($customers),
                customer_id: '{{ old('customer_id', '') }}',
                selectedCustomer: null,

                // Payment state
                paymentType: 'cash', // 'cash' or 'credit'
                payment_method: 'CASH', // 'CASH', 'CBE', 'TELEBIRR', 'ABYSSINIA'
                creditOption: 'full', // 'full' or 'partial'
                amount_paid: 0,
                discount_amount: 0,
                screenshotPreview: null,

                // Quick modals
                showCustomerModal: false,
                savingCustomer: false,
                customerModalError: '',
                newCustomer: { name: '', phone: '' },

                showQuickAddModal: false,
                savingProduct: false,
                quickModalError: '',
                newProduct: { name: '', cost_price: '', current_stock: '' },

                items: [
                    {
                        product_id: '',
                        searchQuery: '',
                        isDropdownOpen: false,
                        mode: 'single', // 'single' or 'pack'
                        singleQty: 1,
                        packType: 'dozen', // 'dozen', 'box', 'pack'
                        pcsPerPack: 12,
                        packCount: 1,
                        quantity: 1, // total pieces calculated
                        pricingChoice: 'pack', // 'pack' or 'piece'
                        unit_price: 0, // price per piece
                        packPrice: 0, // price per pack/dozen
                        cost_price: 0,
                        available_stock: null
                    }
                ],

                init() {
                    // Populate initial product search queries if product_id is preset
                    this.items.forEach(item => {
                        if (item.product_id) {
                            const p = this.catalog.find(prod => prod.id == item.product_id);
                            if (p) {
                                item.searchQuery = p.name;
                                item.cost_price = parseFloat(p.cost_price || 0);
                                item.available_stock = parseInt(p.current_stock || 0);
                            }
                        }
                    });
                    this.onCustomerChange();
                    this.onPaymentTypeChange();
                },

                formatCurrency(num) {
                    const val = parseFloat(num) || 0;
                    return val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                // Product Search & Selection helpers
                getFilteredCatalog(item) {
                    const q = (item.searchQuery || '').trim().toLowerCase();
                    if (!q) {
                        return this.catalog;
                    }
                    return this.catalog.filter(p => {
                        const name = (p.name || '').toLowerCase();
                        const sku = (p.sku || '').toLowerCase();
                        const barcode = (p.barcode || '').toLowerCase();
                        return name.includes(q) || sku.includes(q) || barcode.includes(q);
                    });
                },

                getProductName(id) {
                    const p = this.catalog.find(prod => prod.id == id);
                    return p ? p.name : '';
                },

                selectProduct(item, prod) {
                    item.product_id = prod.id;
                    item.searchQuery = prod.name;
                    item.isDropdownOpen = false;
                    item.cost_price = prod.cost_price !== null ? parseFloat(prod.cost_price) : 0;
                    item.available_stock = parseInt(prod.current_stock || 0);
                    if (!item.unit_price && prod.selling_price > 0) {
                        item.unit_price = parseFloat(prod.selling_price);
                    }
                    if (window.setFormDirty) window.setFormDirty(true);
                    this.computeItem(item);
                },

                clearProductSelection(item) {
                    item.product_id = '';
                    item.searchQuery = '';
                    item.cost_price = 0;
                    item.available_stock = null;
                    item.isDropdownOpen = true;
                    this.computeItem(item);
                },

                selectFirstMatch(item) {
                    const matches = this.getFilteredCatalog(item);
                    if (matches.length > 0) {
                        this.selectProduct(item, matches[0]);
                    }
                },

                onModeChange(item) {
                    if (item.mode === 'single') {
                        item.quantity = parseInt(item.singleQty) || 1;
                    } else {
                        if (item.packType === 'dozen') {
                            item.pcsPerPack = 12;
                        }
                        item.quantity = (parseInt(item.packCount) || 1) * (parseInt(item.pcsPerPack) || 1);
                    }
                    this.computeItem(item);
                },

                onPackTypeChange(item) {
                    if (item.packType === 'dozen') {
                        item.pcsPerPack = 12;
                    } else if (item.pcsPerPack === 12) {
                        item.pcsPerPack = 24;
                    }
                    this.computeItem(item);
                },

                onPricingChoiceChange(item) {
                    this.computeItem(item);
                },

                onPackPriceInput(item) {
                    const pPrice = parseFloat(item.packPrice) || 0;
                    const pcs = parseInt(item.pcsPerPack) || 1;
                    if (pcs > 0) {
                        item.unit_price = parseFloat((pPrice / pcs).toFixed(2));
                    }
                    this.syncTotalPayment();
                },

                onPiecePriceInput(item) {
                    const uPrice = parseFloat(item.unit_price) || 0;
                    const pcs = parseInt(item.pcsPerPack) || 1;
                    item.packPrice = parseFloat((uPrice * pcs).toFixed(2));
                    this.syncTotalPayment();
                },

                computeItem(item) {
                    if (item.mode === 'single') {
                        item.quantity = Math.max(1, parseInt(item.singleQty) || 1);
                    } else {
                        const packs = Math.max(1, parseInt(item.packCount) || 1);
                        const pcs = Math.max(1, parseInt(item.pcsPerPack) || 1);
                        item.quantity = packs * pcs;

                        if (item.pricingChoice === 'pack') {
                            if (item.packPrice > 0) {
                                item.unit_price = parseFloat((item.packPrice / pcs).toFixed(2));
                            }
                        } else {
                            if (item.unit_price > 0) {
                                item.packPrice = parseFloat((item.unit_price * pcs).toFixed(2));
                            }
                        }
                    }
                    this.syncTotalPayment();
                },

                getItemSubtotal(item) {
                    const q = parseFloat(item.quantity) || 0;
                    const p = parseFloat(item.unit_price) || 0;
                    return q * p;
                },

                getItemProfit(item) {
                    const subtotal = this.getItemSubtotal(item);
                    const cost = (parseFloat(item.quantity) || 0) * (parseFloat(item.cost_price) || 0);
                    return Math.max(0, subtotal - cost);
                },

                addItem() {
                    this.items.push({
                        product_id: '',
                        searchQuery: '',
                        isDropdownOpen: false,
                        mode: 'single',
                        singleQty: 1,
                        packType: 'dozen',
                        pcsPerPack: 12,
                        packCount: 1,
                        quantity: 1,
                        pricingChoice: 'pack',
                        unit_price: 0,
                        packPrice: 0,
                        cost_price: 0,
                        available_stock: null
                    });
                    this.$nextTick(() => {
                        if (window.lucide && typeof window.lucide.createIcons === 'function') {
                            window.lucide.createIcons();
                        }
                    });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.syncTotalPayment();
                    }
                },

                calculateGrandTotal() {
                    return this.items.reduce((sum, item) => sum + this.getItemSubtotal(item), 0);
                },

                calculateRemainingCredit() {
                    const grand = this.calculateGrandTotal();
                    const paid = parseFloat(this.amount_paid) || 0;
                    return Math.max(0, grand - paid);
                },

                onPaymentTypeChange() {
                    if (this.paymentType === 'cash') {
                        this.amount_paid = this.calculateGrandTotal();
                    } else {
                        // Credit
                        if (this.creditOption === 'full') {
                            this.amount_paid = 0;
                        }
                    }
                },

                onCreditOptionChange() {
                    if (this.creditOption === 'full') {
                        this.amount_paid = 0;
                    } else {
                        if (this.amount_paid === 0) {
                            this.amount_paid = Math.round(this.calculateGrandTotal() / 2);
                        }
                    }
                },

                onPartialPaidInput() {
                    const grand = this.calculateGrandTotal();
                    if (this.amount_paid > grand) {
                        this.amount_paid = grand;
                    }
                },

                syncTotalPayment() {
                    if (this.paymentType === 'cash') {
                        this.amount_paid = this.calculateGrandTotal();
                    } else if (this.creditOption === 'full') {
                        this.amount_paid = 0;
                    }
                },

                handleScreenshotUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.screenshotPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        this.screenshotPreview = null;
                    }
                },

                removeScreenshot() {
                    this.screenshotPreview = null;
                    const input = document.querySelector('input[name="receipt_screenshot"]');
                    if (input) input.value = '';
                },

                onCustomerSelectChange() {
                    if (this.customer_id === '__NEW__') {
                        this.customer_id = '';
                        this.openQuickCustomerModal();
                    } else {
                        this.onCustomerChange();
                    }
                },

                onCustomerChange() {
                    if (this.customer_id && this.customer_id !== '__NEW__') {
                        this.selectedCustomer = this.customersList.find(c => c.id == this.customer_id) || null;
                    } else {
                        this.selectedCustomer = null;
                    }
                },

                openQuickCustomerModal() {
                    this.customerModalError = '';
                    this.newCustomer = { name: '', phone: '' };
                    this.showCustomerModal = true;
                    this.$nextTick(() => {
                        if (this.$refs && this.$refs.custNameInput) this.$refs.custNameInput.focus();
                    });
                },

                async submitQuickCustomer() {
                    this.customerModalError = '';
                    const name = (this.newCustomer.name || '').trim();
                    if (!name) {
                        this.customerModalError = 'Customer name is required.';
                        return;
                    }

                    this.savingCustomer = true;
                    try {
                        const response = await fetch("{{ route('customers.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                name: name,
                                phone: this.newCustomer.phone || null,
                                is_active: true
                            })
                        });

                        const data = await response.json();
                        if (response.ok && (data.success || data.customer)) {
                            const created = data.customer || data;
                            this.customersList.push(created);
                            this.customer_id = created.id;
                            this.selectedCustomer = created;
                            this.showCustomerModal = false;
                            this.newCustomer = { name: '', phone: '' };
                        } else {
                            this.customerModalError = data.message || 'Could not save customer.';
                        }
                    } catch (e) {
                        this.customerModalError = 'Network error while creating customer.';
                    } finally {
                        this.savingCustomer = false;
                    }
                },

                openQuickAddModal(prefillName = '') {
                    this.quickModalError = '';
                    this.newProduct = { 
                        name: (typeof prefillName === 'string') ? prefillName : '', 
                        cost_price: '', 
                        current_stock: '' 
                    };
                    this.showQuickAddModal = true;
                    this.$nextTick(() => {
                        if (this.$refs && this.$refs.quickNameInput) this.$refs.quickNameInput.focus();
                    });
                },

                async submitQuickProduct() {
                    this.quickModalError = '';
                    const name = (this.newProduct.name || '').trim();
                    if (!name) {
                        this.quickModalError = 'Product name is required.';
                        return;
                    }

                    this.savingProduct = true;
                    try {
                        const cost = parseFloat(this.newProduct.cost_price) || 0;
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
                                selling_price: 0,
                                current_stock: stock
                            })
                        });

                        const data = await response.json();
                        if (response.ok && (data.success || data.product)) {
                            const newProd = data.product || data;
                            this.catalog.push(newProd);

                            let targetItem = this.items.find(i => !i.product_id);
                            if (!targetItem) {
                                targetItem = {
                                    product_id: '',
                                    searchQuery: '',
                                    isDropdownOpen: false,
                                    mode: 'single',
                                    singleQty: 1,
                                    packType: 'dozen',
                                    pcsPerPack: 12,
                                    packCount: 1,
                                    quantity: 1,
                                    pricingChoice: 'pack',
                                    unit_price: 0,
                                    packPrice: 0,
                                    cost_price: 0,
                                    available_stock: null
                                };
                                this.items.push(targetItem);
                            }
                            this.selectProduct(targetItem, newProd);
                            this.showQuickAddModal = false;
                        } else {
                            this.quickModalError = data.message || 'Could not save product.';
                        }
                    } catch (e) {
                        this.quickModalError = 'Network error while creating product.';
                    } finally {
                        this.savingProduct = false;
                    }
                },

                handleSubmit(e) {
                    for (let i = 0; i < this.items.length; i++) {
                        if (!this.items[i].product_id) {
                            e.preventDefault();
                            alert(`⚠️ Please choose a product for Item #${i + 1}. Type in the search box and click to select a product from the list.`);
                            this.items[i].isDropdownOpen = true;
                            return false;
                        }
                    }

                    const remaining = this.calculateRemainingCredit();
                    if (remaining > 0 && !this.customer_id) {
                        e.preventDefault();
                        alert('⚠️ Credit sale requires a Customer! Please select or add a Customer so the system knows who owes this ' + this.formatCurrency(remaining) + ' Birr.');
                        this.openQuickCustomerModal();
                        return false;
                    }

                    if (window.setFormDirty) window.setFormDirty(false);
                    return true;
                }
            };
        }
    </script>
</x-layouts.app>
