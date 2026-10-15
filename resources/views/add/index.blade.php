<x-layouts.app>
    <x-slot:title>Quick Add Hub - Products, Suppliers & Customers</x-slot:title>
    <x-slot:headerTitle>Add Section</x-slot:headerTitle>
    <x-slot:headerSubtitle>Fast & Simple Registration Hub</x-slot:headerSubtitle>

    <div class="max-w-6xl mx-auto space-y-6" 
         x-data="{ 
             currentTab: '{{ $tab ?? 'product' }}',
             switchTab(tab) {
                 this.currentTab = tab;
                 try {
                     const url = new URL(window.location);
                     url.searchParams.set('tab', tab);
                     window.history.replaceState({}, '', url);
                 } catch(e) {}
                 setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
             }
         }">
        
        <!-- Header Banner & Instructions -->
        <div class="glass-card rounded-3xl p-6 sm:p-7 border-2 border-slate-200/80 shadow-xs bg-white">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-800 border border-indigo-200 mb-2">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-indigo-600"></i>
                        <span>Section 2 &bull; Central Add Hub</span>
                    </span>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">What would you like to add?</h1>
                    <p class="text-xs text-slate-600 mt-1 font-semibold">Choose one of the 3 options below to easily register a product, supplier, or customer in seconds.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500">Quick Switch:</span>
                    <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 border border-slate-300">
                        1-Click Access
                    </span>
                </div>
            </div>

            <!-- 3 LARGE, CRYSTAL-CLEAR CHOICE CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6">

                <!-- 1. ADD PRODUCT CARD -->
                <button type="button" 
                        @click="switchTab('product')" 
                        class="text-left p-5 rounded-2xl border-2 transition-all cursor-pointer relative overflow-hidden group focus:outline-none"
                        :class="currentTab === 'product' 
                            ? 'bg-emerald-500/10 border-emerald-500 shadow-lg ring-2 ring-emerald-500/30 -translate-y-0.5' 
                            : 'bg-white border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/30'">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold transition-transform group-hover:scale-105"
                             :class="currentTab === 'product' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-500/30' : 'bg-emerald-100 text-emerald-800'">
                            <i data-lucide="package-plus" class="w-6 h-6"></i>
                        </div>
                        <span x-show="currentTab === 'product'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-emerald-600 text-white shadow-xs">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            Selected
                        </span>
                        <span x-show="currentTab !== 'product'" class="text-[11px] font-bold text-slate-400 group-hover:text-emerald-600 transition-colors">
                            Click to select &rarr;
                        </span>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-md bg-emerald-600 text-white text-[10px] font-black flex items-center justify-center shrink-0">1</span>
                            <h3 class="text-base font-black text-slate-900 group-hover:text-emerald-700 transition-colors">Add Product</h3>
                        </div>
                        <p class="text-xs text-slate-600 mt-1.5 font-semibold leading-relaxed">
                            Register inventory items, purchase costs, selling prices & starting stock levels.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between text-[11px] font-black text-emerald-700">
                        <span>📦 Products Form</span>
                        <span class="group-hover:translate-x-1 transition-transform font-extrabold">&rarr;</span>
                    </div>
                </button>

                <!-- 2. ADD SUPPLIER CARD -->
                <button type="button" 
                        @click="switchTab('supplier')" 
                        class="text-left p-5 rounded-2xl border-2 transition-all cursor-pointer relative overflow-hidden group focus:outline-none"
                        :class="currentTab === 'supplier' 
                            ? 'bg-amber-500/10 border-amber-500 shadow-lg ring-2 ring-amber-500/30 -translate-y-0.5' 
                            : 'bg-white border-slate-200 hover:border-amber-300 hover:bg-amber-50/30'">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold transition-transform group-hover:scale-105"
                             :class="currentTab === 'supplier' ? 'bg-amber-600 text-white shadow-md shadow-amber-500/30' : 'bg-amber-100 text-amber-800'">
                            <i data-lucide="truck" class="w-6 h-6"></i>
                        </div>
                        <span x-show="currentTab === 'supplier'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-amber-600 text-white shadow-xs">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            Selected
                        </span>
                        <span x-show="currentTab !== 'supplier'" class="text-[11px] font-bold text-slate-400 group-hover:text-amber-600 transition-colors">
                            Click to select &rarr;
                        </span>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-md bg-amber-600 text-white text-[10px] font-black flex items-center justify-center shrink-0">2</span>
                            <h3 class="text-base font-black text-slate-900 group-hover:text-amber-700 transition-colors">Add Supplier</h3>
                        </div>
                        <p class="text-xs text-slate-600 mt-1.5 font-semibold leading-relaxed">
                            Register vendor contacts, company names, warehouse addresses & phone numbers.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between text-[11px] font-black text-amber-700">
                        <span>🚚 Suppliers Form</span>
                        <span class="group-hover:translate-x-1 transition-transform font-extrabold">&rarr;</span>
                    </div>
                </button>

                <!-- 3. ADD CUSTOMER CARD -->
                <button type="button" 
                        @click="switchTab('customer')" 
                        class="text-left p-5 rounded-2xl border-2 transition-all cursor-pointer relative overflow-hidden group focus:outline-none"
                        :class="currentTab === 'customer' 
                            ? 'bg-cyan-500/10 border-cyan-500 shadow-lg ring-2 ring-cyan-500/30 -translate-y-0.5' 
                            : 'bg-white border-slate-200 hover:border-cyan-300 hover:bg-cyan-50/30'">
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold transition-transform group-hover:scale-105"
                             :class="currentTab === 'customer' ? 'bg-cyan-600 text-white shadow-md shadow-cyan-500/30' : 'bg-cyan-100 text-cyan-800'">
                            <i data-lucide="user-plus" class="w-6 h-6"></i>
                        </div>
                        <span x-show="currentTab === 'customer'" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-black bg-cyan-600 text-white shadow-xs">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            Selected
                        </span>
                        <span x-show="currentTab !== 'customer'" class="text-[11px] font-bold text-slate-400 group-hover:text-cyan-600 transition-colors">
                            Click to select &rarr;
                        </span>
                    </div>
                    <div class="mt-4">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-md bg-cyan-600 text-white text-[10px] font-black flex items-center justify-center shrink-0">3</span>
                            <h3 class="text-base font-black text-slate-900 group-hover:text-cyan-700 transition-colors">Add Customer</h3>
                        </div>
                        <p class="text-xs text-slate-600 mt-1.5 font-semibold leading-relaxed">
                            Register client buyers, phone numbers, delivery addresses & credit agreements.
                        </p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-200 flex items-center justify-between text-[11px] font-black text-cyan-700">
                        <span>👥 Customers Form</span>
                        <span class="group-hover:translate-x-1 transition-transform font-extrabold">&rarr;</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SECTION 1: ADD PRODUCT FORM & RECENT PANEL -->
        <!-- ========================================== -->
        <div x-show="currentTab === 'product'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="glass-card rounded-3xl p-6 sm:p-8 border-2 border-emerald-300 bg-white shadow-sm">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Area -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black">
                                <i data-lucide="package-plus" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-900">New Product Form</h2>
                                <p class="text-xs text-slate-600 font-semibold">Enter item name, buying cost, and stock quantity.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">* Required</span>
                    </div>

                    <form method="POST" action="{{ route('products.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="redirect_to" value="add">

                        <!-- Product Name -->
                        <div>
                            <label for="prod_name" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                Product Name <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" id="prod_name" name="name" required placeholder="e.g. Basmati Rice 5kg, Wireless Mouse, Cement 50kg..." 
                                   class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 placeholder:text-slate-400 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        </div>

                        <!-- Cost & Selling Price Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div>
                                <label for="prod_cost_price" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Buying Cost (ETB) <span class="text-rose-600">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-slate-500 font-black text-xs">ETB</span>
                                    <input type="number" step="0.01" min="0" id="prod_cost_price" name="cost_price" required placeholder="0.00" onfocus="this.select()"
                                           class="w-full pl-12 pr-3 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-black text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                </div>
                                <span class="text-[11px] font-bold text-slate-600 mt-1 block">Purchase cost (የግዢ ዋጋ)</span>
                            </div>

                            <div>
                                <label for="prod_selling_price" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Selling Price (Optional)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-slate-500 font-black text-xs">ETB</span>
                                    <input type="number" step="0.01" min="0" id="prod_selling_price" name="selling_price" placeholder="Set at sale" onfocus="this.select()"
                                           class="w-full pl-12 pr-3 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-black text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                </div>
                                <span class="text-[11px] font-bold text-slate-600 mt-1 block">Sell price (የመሸጫ ዋጋ)</span>
                            </div>

                            <div>
                                <label for="prod_current_stock" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Initial Quantity <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" min="0" id="prod_current_stock" name="current_stock" value="0" required onfocus="this.select()"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-black text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                                <span class="text-[11px] font-bold text-slate-600 mt-1 block">Current stock in shop</span>
                            </div>
                        </div>

                        <!-- Category, Unit, Alert Level -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div>
                                <label for="prod_category" class="block text-xs font-bold text-slate-800 mb-1.5">Category (Optional)</label>
                                <select id="prod_category" name="category_id" class="w-full px-3 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-xs font-bold text-slate-800 focus:border-emerald-600 focus:outline-none">
                                    <option value="">-- General (No Category) --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="prod_unit" class="block text-xs font-bold text-slate-800 mb-1.5">Unit of Measure</label>
                                <input type="text" id="prod_unit" name="unit" value="Pcs" placeholder="Pcs, Kg, Box, Bag..." 
                                       class="w-full px-3 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-xs font-bold text-slate-800 focus:border-emerald-600 focus:outline-none">
                            </div>

                            <div>
                                <label for="prod_min_stock" class="block text-xs font-bold text-slate-800 mb-1.5">Low Stock Alert Level</label>
                                <input type="number" min="0" id="prod_min_stock" name="minimum_stock_level" value="5" 
                                       class="w-full px-3 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-xs font-bold text-slate-800 focus:border-emerald-600 focus:outline-none">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                            <a href="{{ route('products.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-xs font-bold transition-colors">
                                View Full Products Catalog &rarr;
                            </a>
                            <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Save & Add Product</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Side: Recent Products Preview -->
                <div class="rounded-2xl bg-slate-50 border-2 border-slate-200 p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <span class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-emerald-600"></i>
                                Recently Added
                            </span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        </div>
                        <div class="mt-3 space-y-2.5">
                            @forelse($recentProducts as $rp)
                                <div class="p-3 rounded-xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
                                    <div class="min-w-0 pr-2">
                                        <p class="text-xs font-black text-slate-900 truncate">{{ $rp->name }}</p>
                                        <p class="text-[11px] text-slate-600 font-bold">
                                            Cost: {{ number_format($rp->cost_price, 2) }} ETB
                                        </p>
                                    </div>
                                    <span class="text-[11px] font-black px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-300 shrink-0">
                                        {{ $rp->current_stock }} in stock
                                    </span>
                                </div>
                            @empty
                                <div class="py-8 text-center text-xs text-slate-400 font-semibold">
                                    No products registered yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 mt-4">
                        <a href="{{ route('products.index') }}" class="block text-center text-xs font-black text-emerald-700 hover:text-emerald-800">
                            Manage All Products ({{ count($recentProducts) }}+) &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================== -->
        <!-- SECTION 2: ADD SUPPLIER FORM & RECENT PANEL -->
        <!-- =========================================== -->
        <div x-show="currentTab === 'supplier'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="glass-card rounded-3xl p-6 sm:p-8 border-2 border-amber-300 bg-white shadow-sm">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Area -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-600 text-white flex items-center justify-center font-black">
                                <i data-lucide="truck" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-900">New Supplier Form</h2>
                                <p class="text-xs text-slate-600 font-semibold">Register vendor contact person, company, and phone number.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">* Required</span>
                    </div>

                    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="redirect_to" value="add">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="sup_name" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Supplier / Contact Person <span class="text-rose-600">*</span>
                                </label>
                                <input type="text" id="sup_name" name="name" required placeholder="e.g. John Doe, Ahmed Hassan..." 
                                       class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-amber-600 focus:outline-none focus:ring-4 focus:ring-amber-100">
                            </div>

                            <div>
                                <label for="sup_company" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Company / Business Name
                                </label>
                                <input type="text" id="sup_company" name="company_name" placeholder="e.g. Apex Wholesale Trading Ltd." 
                                       class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-amber-600 focus:outline-none focus:ring-4 focus:ring-amber-100">
                            </div>

                            <div>
                                <label for="sup_phone" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Phone Number <span class="text-rose-600">*</span>
                                </label>
                                <input type="text" id="sup_phone" name="phone" required placeholder="e.g. +251 911 000 000 or (555) 012-3456" 
                                       class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-amber-600 focus:outline-none focus:ring-4 focus:ring-amber-100">
                            </div>

                            <div>
                                <label for="sup_email" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Email Address (Optional)
                                </label>
                                <input type="email" id="sup_email" name="email" placeholder="orders@supplier.com" 
                                       class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-amber-600 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="sup_address" class="block text-xs font-bold text-slate-800 mb-1.5">Warehouse Address / Location</label>
                            <input type="text" id="sup_address" name="address" placeholder="Street, City, Warehouse #..." 
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-xs font-bold text-slate-800 focus:border-amber-600 focus:outline-none">
                        </div>

                        <div>
                            <label for="sup_notes" class="block text-xs font-bold text-slate-800 mb-1.5">Payment Terms / Notes</label>
                            <input type="text" id="sup_notes" name="notes" placeholder="e.g. Cash on delivery, Net-15 days, Bank transfer instructions..." 
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-xs font-bold text-slate-800 focus:border-amber-600 focus:outline-none">
                        </div>

                        <input type="hidden" name="is_active" value="1">

                        <!-- Action Buttons -->
                        <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                            <a href="{{ route('suppliers.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-xs font-bold transition-colors">
                                View All Suppliers Directory &rarr;
                            </a>
                            <button type="submit" class="px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-black shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Save & Register Supplier</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Side: Recent Suppliers Preview -->
                <div class="rounded-2xl bg-slate-50 border-2 border-slate-200 p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <span class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-600"></i>
                                Recently Registered
                            </span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        </div>
                        <div class="mt-3 space-y-2.5">
                            @forelse($recentSuppliers as $rs)
                                <div class="p-3 rounded-xl bg-white border border-slate-200 shadow-xs">
                                    <p class="text-xs font-black text-slate-900">{{ $rs->name }}</p>
                                    @if($rs->company_name)
                                        <p class="text-[11px] font-bold text-slate-700 mt-0.5">{{ $rs->company_name }}</p>
                                    @endif
                                    <p class="text-[11px] text-amber-800 font-bold mt-1">
                                        📞 {{ $rs->phone }}
                                    </p>
                                </div>
                            @empty
                                <div class="py-8 text-center text-xs text-slate-400 font-semibold">
                                    No suppliers registered yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 mt-4">
                        <a href="{{ route('suppliers.index') }}" class="block text-center text-xs font-black text-amber-700 hover:text-amber-800">
                            Manage All Suppliers &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================== -->
        <!-- SECTION 3: ADD CUSTOMER FORM & RECENT PANEL -->
        <!-- =========================================== -->
        <div x-show="currentTab === 'customer'" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="glass-card rounded-3xl p-6 sm:p-8 border-2 border-cyan-300 bg-white shadow-sm">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Area -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-cyan-600 text-white flex items-center justify-center font-black">
                                <i data-lucide="user-plus" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-900">New Customer Form</h2>
                                <p class="text-xs text-slate-600 font-semibold">Register customer full name, contact phone, and account terms.</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">* Required</span>
                    </div>

                    <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="redirect_to" value="add">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label for="cust_name" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Customer Full Name / Business <span class="text-rose-600">*</span>
                                </label>
                                <input type="text" id="cust_name" name="name" required placeholder="e.g. Sara Tekle, City Pharmacy, David Miller..." 
                                       class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            </div>

                            <div>
                                <label for="cust_phone" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Phone Number (Optional)
                                </label>
                                <input type="text" id="cust_phone" name="phone" placeholder="e.g. +251 912 345 678" 
                                       class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-cyan-600 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            </div>

                            <div>
                                <label for="cust_email" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-1.5">
                                    Email Address (Optional)
                                </label>
                                <input type="email" id="cust_email" name="email" placeholder="customer@email.com" 
                                       class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-cyan-600 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label for="cust_address" class="block text-xs font-bold text-slate-800 mb-1.5">Billing / Delivery Address</label>
                            <input type="text" id="cust_address" name="address" placeholder="Address, District, City..." 
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-xs font-bold text-slate-800 focus:border-cyan-600 focus:outline-none">
                        </div>

                        <div>
                            <label for="cust_notes" class="block text-xs font-bold text-slate-800 mb-1.5">Credit Agreement / Customer Notes</label>
                            <input type="text" id="cust_notes" name="notes" placeholder="Credit limit, discount notes, special terms..." 
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-xs font-bold text-slate-800 focus:border-cyan-600 focus:outline-none">
                        </div>

                        <input type="hidden" name="is_active" value="1">

                        <!-- Action Buttons -->
                        <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                            <a href="{{ route('customers.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-xs font-bold transition-colors">
                                View All Customers Directory &rarr;
                            </a>
                            <button type="submit" class="px-6 py-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-black shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Save & Register Customer</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Side: Recent Customers Preview -->
                <div class="rounded-2xl bg-slate-50 border-2 border-slate-200 p-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <span class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-cyan-600"></i>
                                Recently Registered
                            </span>
                            <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 animate-pulse"></span>
                        </div>
                        <div class="mt-3 space-y-2.5">
                            @forelse($recentCustomers as $rc)
                                <div class="p-3 rounded-xl bg-white border border-slate-200 shadow-xs">
                                    <p class="text-xs font-black text-slate-900">{{ $rc->name }}</p>
                                    @if($rc->phone)
                                        <p class="text-[11px] text-cyan-800 font-bold mt-1">📞 {{ $rc->phone }}</p>
                                    @endif
                                </div>
                            @empty
                                <div class="py-8 text-center text-xs text-slate-400 font-semibold">
                                    No customers registered yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 mt-4">
                        <a href="{{ route('customers.index') }}" class="block text-center text-xs font-black text-cyan-700 hover:text-cyan-800">
                            Manage All Customers &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
