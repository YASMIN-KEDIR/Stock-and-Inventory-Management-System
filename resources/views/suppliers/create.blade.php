<x-layouts.app>
    <x-slot:title>New Supplier</x-slot:title>
    <x-slot:headerTitle>Supply Chain</x-slot:headerTitle>
    <x-slot:headerSubtitle>Register Supplier</x-slot:headerSubtitle>

    <div class="max-w-3xl mx-auto">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-6">Register New Supplier Profile</h2>

            <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Contact Person *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. John Apex" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Apex Tech Distributors Ltd." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Phone Number *</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="+1 (555) 000-0000" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('phone') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="orders@company.com" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Physical Location / Warehouse Address</label>
                    <textarea id="address" name="address" rows="2" placeholder="Street, Suite, City, State..." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Terms & Internal Notes</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Net-30, bank account wire instructions, contact schedule..." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <label for="is_active" class="text-sm font-medium text-slate-700 cursor-pointer">Active Supplier</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('suppliers.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">Register Supplier</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
