<x-layouts.app>
    <x-slot:title>New Product</x-slot:title>
    <x-slot:headerTitle>Inventory Management</x-slot:headerTitle>
    <x-slot:headerSubtitle>Add Product</x-slot:headerSubtitle>

    <div class="max-w-4xl mx-auto">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Create New Catalog Product</h2>

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Basic Details Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Product Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Logitech MX Master 3S Wireless Mouse" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">SKU (Unique Code) *</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku') }}" required placeholder="e.g. LOGI-MX3S-BLK" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('sku') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="barcode" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Barcode / UPC (Optional)</label>
                        <input type="text" id="barcode" name="barcode" value="{{ old('barcode') }}" placeholder="e.g. 097855173560" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('barcode') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Category *</label>
                        <select id="category_id" name="category_id" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="supplier_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Preferred Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">No Default Supplier</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->company_name ?? $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="unit" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Unit of Measure *</label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit', 'Pcs') }}" required placeholder="Pcs, Kg, Box, Litre, Meter" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="minimum_stock_level" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Safety / Reorder Alert Level *</label>
                        <input type="number" id="minimum_stock_level" name="minimum_stock_level" value="{{ old('minimum_stock_level', 5) }}" required min="0" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="cost_price" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Purchase Cost Price ($) *</label>
                        <input type="number" step="0.01" id="cost_price" name="cost_price" value="{{ old('cost_price', '0.00') }}" required min="0" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="selling_price" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Selling Retail Price ($) *</label>
                        <input type="number" step="0.01" id="selling_price" name="selling_price" value="{{ old('selling_price', '0.00') }}" required min="0" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="current_stock" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Initial Opening Stock *</label>
                        <input type="number" id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" required min="0" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[11px] text-slate-400 mt-1">Will be recorded in stock transaction ledger.</p>
                    </div>

                    <div>
                        <label for="image" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Product Photo</label>
                        <input type="file" id="image" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Product Description / Specifications</label>
                    <textarea id="description" name="description" rows="3" placeholder="Specifications, warranty, compatibility details..." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <label for="is_active" class="text-sm font-medium text-slate-700 cursor-pointer">Product is Active for Commercial Sales</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
