<x-layouts.app>
    <x-slot:title>Edit Supplier</x-slot:title>
    <x-slot:headerTitle>Supply Chain</x-slot:headerTitle>
    <x-slot:headerSubtitle>Edit Supplier Profile</x-slot:headerSubtitle>

    <div class="max-w-3xl mx-auto">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Edit Supplier: {{ $supplier->company_name ?? $supplier->name }}</h2>

            <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Contact Person *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Phone Number *</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" required class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Physical Location Address</label>
                    <textarea id="address" name="address" rows="2" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('address', $supplier->address) }}</textarea>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Terms & Notes</label>
                    <textarea id="notes" name="notes" rows="2" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $supplier->notes) }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <label for="is_active" class="text-sm font-medium text-slate-700 cursor-pointer">Active Supplier</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
