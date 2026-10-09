<x-layouts.app>
    <x-slot:title>Edit Product</x-slot:title>
    <x-slot:headerTitle>Inventory Management</x-slot:headerTitle>
    <x-slot:headerSubtitle>Edit Product</x-slot:headerSubtitle>

    <div class="max-w-4xl mx-auto">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Edit Product: {{ $product->name }}</h2>

            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Product Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                        @error('name') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sku" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">SKU *</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                        @error('sku') <p class="text-xs font-bold text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="barcode" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Barcode / UPC</label>
                        <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-mono font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-bold text-slate-900 mb-2">Category (Optional)</label>
                        <select id="category_id" name="category_id" class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-semibold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                            <option value="">None / General (No Category)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="supplier_id" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Preferred Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-semibold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                            <option value="">No Default Supplier</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->company_name ?? $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="unit" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Unit *</label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div>
                        <label for="minimum_stock_level" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Safety / Reorder Alert Level *</label>
                        <input type="number" id="minimum_stock_level" name="minimum_stock_level" value="{{ old('minimum_stock_level', $product->minimum_stock_level) }}" required min="0" placeholder="5" onfocus="this.select()" class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div>
                        <label for="cost_price" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Cost Price ($) *</label>
                        <input type="number" step="0.01" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required min="0" placeholder="0.00" onfocus="this.select()" class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div>
                        <label for="selling_price" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Selling Retail Price ($) *</label>
                        <input type="number" step="0.01" id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required min="0" placeholder="0.00" onfocus="this.select()" class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="image" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Update Photo</label>
                        <input type="file" id="image" name="image" accept="image/*" class="w-full text-xs font-semibold text-slate-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 cursor-pointer">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-900 mb-2">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 rounded-xl bg-white border-2 border-slate-300 text-sm font-medium text-slate-900 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <label for="is_active" class="text-sm font-medium text-slate-700 cursor-pointer">Product is Active</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
