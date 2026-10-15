<x-layouts.app>
    <x-slot:title>New Customer</x-slot:title>
    <x-slot:headerTitle>Commercial Relations</x-slot:headerTitle>
    <x-slot:headerSubtitle>Add Customer Profile</x-slot:headerSubtitle>

    <div class="max-w-3xl mx-auto">
            <!-- Customer Create Header & Quick Switcher -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 mb-6">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-200 mb-1">
                        Section 3 &bull; Users & Customers
                    </span>
                    <h2 class="text-xl font-black text-slate-900">Register Customer Account</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Quickly register customer profile, phone & contact details.</p>
                </div>
                
                <div class="flex items-center bg-slate-100 p-1.5 rounded-xl border border-slate-200">
                    <a href="{{ route('customers.index') }}" class="px-3 py-1.5 rounded-lg text-slate-700 hover:text-slate-900 font-bold text-xs transition-colors">
                        👥 View Customers
                    </a>
                    <a href="{{ route('suppliers.create') }}" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 font-bold text-xs transition-colors">
                        🏢 Add Supplier
                    </a>
                    <a href="{{ route('products.create') }}" class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 font-bold text-xs transition-colors">
                        📦 Add Product
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('customers.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Customer Full Name / Company *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. David Miller" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="david@company.com" class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Billing / Delivery Address</label>
                    <textarea id="address" name="address" rows="2" placeholder="Street address, city, state..." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Credit Terms & Customer Notes</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Credit limits, payment promises, terms..." class="w-full px-4 py-3 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <label for="is_active" class="text-sm font-medium text-slate-700 cursor-pointer">Customer Account is Active</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('customers.index') }}" class="px-5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 text-sm font-semibold">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">Register Customer</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
