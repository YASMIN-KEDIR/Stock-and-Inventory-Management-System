<x-layouts.app>
    <x-slot:title>New Product</x-slot:title>
    <x-slot:headerTitle>Inventory Management</x-slot:headerTitle>
    <x-slot:headerSubtitle>Add Product</x-slot:headerSubtitle>

    <div class="max-w-4xl mx-auto">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs bg-white">
            <!-- Product Create Header & Quick Switcher -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 mb-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-200 mb-1">
                        Section 2 &bull; Products Management
                    </span>
                    <h2 class="text-xl font-black text-slate-900">Add New Product</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Quick & easy product entry. Only product name and buying cost are required.</p>
                </div>
                
                <div class="flex items-center bg-slate-100 p-1.5 rounded-xl border border-slate-200">
                    <a href="{{ route('products.index') }}" class="px-3 py-1.5 rounded-lg text-slate-700 hover:text-slate-900 font-bold text-xs transition-colors">
                        📦 View All Products
                    </a>
                    <a href="{{ route('suppliers.create') }}" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 font-bold text-xs transition-colors">
                        🏢 Add Supplier
                    </a>
                    <a href="{{ route('customers.create') }}" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 font-bold text-xs transition-colors">
                        👥 Add Customer
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Main Essential Information -->
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">
                            Product Name <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="e.g. Wireless Mouse, Red Shirt, Coffee Beans" class="w-full px-4 py-3 rounded-2xl bg-white border-2 border-slate-300 text-base font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        @error('name') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Pricing & Stock Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="cost_price" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1.5">
                                Buying Cost (Birr) <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" step="0.01" id="cost_price" name="cost_price" value="{{ old('cost_price') }}" required min="0" placeholder="0.00" onfocus="this.select()" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                            <span class="text-xs font-semibold text-slate-600 mt-1 block">What you paid to buy it (የግዢ ዋጋ)</span>
                        </div>

                        <div>
                            <label for="selling_price" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1.5">
                                Selling Price (Birr - Optional)
                            </label>
                            <input type="number" step="0.01" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" min="0" placeholder="Set at sale" onfocus="this.select()" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                            <span class="text-xs font-semibold text-slate-600 mt-1 block">Determined upon sale (የመሸጫ ዋጋ)</span>
                        </div>

                        <div>
                            <label for="current_stock" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-1.5">
                                Opening Stock Quantity
                            </label>
                            <input type="number" id="current_stock" name="current_stock" value="{{ old('current_stock') }}" placeholder="0" onfocus="this.select()" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                            <span class="text-xs font-semibold text-slate-600 mt-1 block">Initial quantity in shop</span>
                        </div>
                    </div>
                </div>

                <!-- Secondary / Optional Section -->
                <div class="pt-5 border-t border-slate-200">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-800 mb-4">Optional Details (Can be left blank)</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_id" class="block text-xs font-bold text-slate-800 mb-1.5">Supplier (Optional)</label>
                            <select id="supplier_id" name="supplier_id" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-semibold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                                <option value="">No Default Supplier</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->company_name ?? $sup->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="unit" class="block text-xs font-bold text-slate-800 mb-1.5">Unit of Measure</label>
                            <input type="text" id="unit" name="unit" value="{{ old('unit', 'Pcs') }}" placeholder="Pcs, Kg, Box, Bottle" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        </div>

                        <div>
                            <label for="barcode" class="block text-xs font-bold text-slate-800 mb-1.5">Barcode / Scanner Code (Optional)</label>
                            <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}" placeholder="e.g. 097855173560" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-mono font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        </div>

                        <div>
                            <label for="minimum_stock_level" class="block text-xs font-bold text-slate-800 mb-1.5">Low Stock Alert Level</label>
                            <input type="number" id="minimum_stock_level" name="minimum_stock_level" value="{{ old('minimum_stock_level') }}" min="0" placeholder="5" onfocus="this.select()" class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="description" class="block text-xs font-bold text-slate-800 mb-1.5">Notes / Description (Optional)</label>
                        <textarea id="description" name="description" rows="2" placeholder="Optional notes about the item..." class="w-full px-4 py-2.5 rounded-xl bg-white border-2 border-slate-300 text-sm font-medium text-slate-900 focus:border-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100">{{ old('description') }}</textarea>
                    </div>

                    <div class="mt-4">
                        <label for="image" class="block text-xs font-bold text-slate-800 mb-1.5">Product Photo (Optional)</label>
                        <input type="file" id="image" name="image" accept="image/*" class="w-full text-xs font-semibold text-slate-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 cursor-pointer">
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <label for="is_active" class="text-sm font-medium text-slate-700 cursor-pointer">Product is Active (Ready for selling)</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-md shadow-indigo-500/20 transition-all flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Save Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
