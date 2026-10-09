<x-layouts.app>
    <x-slot:title>Product Catalog</x-slot:title>
    <x-slot:headerTitle>Inventory Management</x-slot:headerTitle>
    <x-slot:headerSubtitle>Product Master Catalog</x-slot:headerSubtitle>

    <div class="space-y-6">
        <!-- Filter & Search Bar -->
        <div class="glass-card rounded-3xl p-5 border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU, or barcode..." class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Category Select -->
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <!-- Stock Status Filter -->
                    <select name="filter" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-700">
                        <option value="">All Stock Levels</option>
                        <option value="low_stock" {{ request('filter') === 'low_stock' ? 'selected' : '' }}>⚠️ Low Stock Only</option>
                        <option value="out_of_stock" {{ request('filter') === 'out_of_stock' ? 'selected' : '' }}>🚨 Out of Stock Only</option>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all">Filter</button>
                    @if(request()->hasAny(['search', 'category_id', 'filter']))
                        <a href="{{ route('products.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition-all">Reset</a>
                    @endif
                    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs transition-all">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Add Product</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Master Table -->
        <div class="glass-card rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-6 py-4">Product Info</th>
                            <th class="px-6 py-4">Category & Supplier</th>
                            <th class="px-6 py-4 text-right">Cost Price</th>
                            <th class="px-6 py-4 text-right">Selling Price</th>
                            <th class="px-6 py-4 text-center">Current Stock</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('products.show', $product) }}" class="font-bold text-slate-900 hover:text-indigo-600 block transition-colors">
                                        {{ $product->name }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-400 font-mono">
                                        <span>SKU: {{ $product->sku }}</span>
                                        @if($product->barcode)
                                            <span>&bull; UPC: {{ $product->barcode }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md block w-fit">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </span>
                                    <span class="text-xs text-slate-500 mt-1 block">
                                        {{ $product->supplier->company_name ?? $product->supplier->name ?? 'No Default Supplier' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-600">
                                    ${{ number_format($product->cost_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-900">
                                    ${{ number_format($product->selling_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold text-sm {{ $product->current_stock <= 0 ? 'text-rose-600' : ($product->current_stock <= $product->minimum_stock_level ? 'text-amber-600' : 'text-slate-900') }}">
                                        {{ $product->current_stock }} {{ $product->unit }}
                                    </span>
                                    <span class="block text-[11px] text-slate-400">Min: {{ $product->minimum_stock_level }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($product->current_stock <= 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold badge-outofstock">
                                            Out of Stock
                                        </span>
                                    @elseif($product->current_stock <= $product->minimum_stock_level)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold badge-lowstock">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold badge-instock">
                                            In Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('products.show', $product) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-colors" title="View Details & Ledger">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-colors" title="Edit Product">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition-colors" title="Delete Product">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    <div class="max-w-sm mx-auto">
                                        <i data-lucide="package-plus" class="w-12 h-12 text-emerald-500 mx-auto mb-3"></i>
                                        <p class="font-bold text-slate-800 text-base">No Products in Catalog Yet</p>
                                        <p class="text-xs text-slate-500 mt-1 mb-4">Start by adding the items you sell in your shop.</p>
                                        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition-all">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                            <span>Add Your First Product</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
