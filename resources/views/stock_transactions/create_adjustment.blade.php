<x-layouts.app>
    <x-slot:title>Manual Stock Adjustment</x-slot:title>
    <x-slot:headerTitle>Inventory Audit</x-slot:headerTitle>
    <x-slot:headerSubtitle>Physical Stock Correction & Damage Log</x-slot:headerSubtitle>

    <div class="max-w-2xl mx-auto" x-data="adjustmentForm()">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Perform Stock Quantity Adjustment</h2>

            <form method="POST" action="{{ route('stock-adjustments.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="product_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Select Product *</label>
                    <select id="product_id" name="product_id" x-model="selectedProductId" @change="updateStock()" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choose item to adjust...</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} (Current Stock: {{ $p->current_stock }} {{ $p->unit }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900 text-xs" x-show="currentStock !== null">
                    <span class="font-bold">Current Physical Stock:</span>
                    <span class="font-mono text-sm font-extrabold ml-1" x-text="`${currentStock} Units`"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="adjustment_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Adjustment Action *</label>
                        <select id="adjustment_type" name="adjustment_type" x-model="adjType" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="ADDITION">Addition (+) Extra / Found Stock</option>
                            <option value="SUBTRACTION">Subtraction (-) Damage / Loss / Expired</option>
                        </select>
                    </div>

                    <div>
                        <label for="quantity" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Adjustment Quantity *</label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" required min="1" placeholder="1" onfocus="this.select()" :max="adjType === 'SUBTRACTION' ? currentStock : 999999" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('quantity') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Mandatory Audit Reason *</label>
                    <textarea id="reason" name="reason" rows="3" required placeholder="Describe reason for adjustment (e.g. Broken in warehouse transit, physical inventory discrepancy, return from client)..." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('reason') }}</textarea>
                    @error('reason') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('stock-transactions.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold shadow-xs">Record Stock Adjustment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function adjustmentForm() {
            return {
                catalog: @json($products),
                selectedProductId: '',
                currentStock: null,
                adjType: 'ADDITION',

                updateStock() {
                    const prod = this.catalog.find(p => p.id == this.selectedProductId);
                    if (prod) {
                        this.currentStock = parseInt(prod.current_stock);
                    } else {
                        this.currentStock = null;
                    }
                }
            }
        }
    </script>
</x-layouts.app>
