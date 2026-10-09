<x-layouts.guest>
    <x-slot:title>Sign In</x-slot:title>

    <div class="bg-white border-2 border-slate-200 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/50">
        <!-- Logo Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-indigo-600 items-center justify-center text-white shadow-lg shadow-emerald-600/30 mb-4">
                <i data-lucide="shopping-bag" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Merkato<span class="text-emerald-600">.</span></h1>
            <p class="text-xs text-emerald-800 font-bold uppercase tracking-wider mt-1 bg-emerald-50 py-1 px-3 rounded-full inline-block border border-emerald-200">Retail POS & Inventory System</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-bold flex items-center gap-2.5">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-300 text-rose-900 text-sm font-bold flex items-center gap-2.5">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </div>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', 'admin@merkato.com') }}" 
                           required 
                           autocomplete="email" 
                           placeholder="admin@merkato.com" 
                           class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border-2 border-slate-300 text-slate-900 font-bold placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-black uppercase tracking-wider text-slate-800 mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           value="password123" 
                           required 
                           autocomplete="current-password" 
                           placeholder="••••••••••••" 
                           class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-50 border-2 border-slate-300 text-slate-900 font-bold placeholder-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-white border-2 border-slate-400 text-emerald-600 focus:ring-emerald-500">
                    <span>Remember me</span>
                </label>
                <span class="text-slate-600 font-semibold bg-slate-100 px-2 py-1 rounded-md">Default: password123</span>
            </div>

            <button type="submit" class="w-full py-4 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-base shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                <span>Sign In to Merkato</span>
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
        </form>

        <!-- Credentials Quick Info -->
        <div class="mt-8 pt-6 border-t border-slate-200 text-center">
            <p class="text-xs text-slate-600 font-semibold">
                Cashier & Store Management Portal &bull; Merkato POS
            </p>
        </div>
    </div>
</x-layouts.guest>
