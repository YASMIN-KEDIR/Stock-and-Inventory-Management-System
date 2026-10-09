<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | {{ config('app.name', 'Merkato') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Local High-Speed Scripts (Zero External CDN Latency) -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <script src="{{ asset('js/lucide.min.js') }}"></script>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-900/5 selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false, profileDropdown: false }">
    <div class="min-h-full flex flex-col lg:flex-row">

        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-slate-900/70 backdrop-blur-xs lg:hidden"
             @click="sidebarOpen = false"></div>

        <!-- Sidebar Navigation (High-Contrast, Ultra-Readable) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r-2 border-slate-200 text-slate-900 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:h-screen lg:shrink-0 shadow-sm">
            
            <!-- App Brand / Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b-2 border-slate-100 bg-slate-50">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 via-teal-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-emerald-600/25 group-hover:scale-105 transition-transform duration-200">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-slate-900 block">Merkato<span class="text-emerald-600">.</span></span>
                        <span class="text-[10px] font-black tracking-wider text-emerald-800 uppercase">Retail POS & Finance</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-700 hover:text-slate-900 p-1.5 rounded-lg hover:bg-slate-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-5 overflow-y-auto space-y-6">
                <!-- Cashier Main Action -->
                <div>
                    <span class="px-3 text-xs font-black uppercase tracking-wider text-emerald-800">⚡ Point of Sale</span>
                    <div class="mt-2 space-y-1.5">
                        <a href="{{ route('sales.create') }}" 
                           class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-black transition-all bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-600/25 hover:brightness-110">
                            <i data-lucide="zap" class="w-5 h-5 text-amber-300 animate-pulse"></i>
                            <span>Open Cashier POS (Sell)</span>
                        </a>
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="layout-dashboard" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Store Summary</span>
                        </a>
                    </div>
                </div>

                <!-- Sales & Invoices -->
                <div>
                    <span class="px-3 text-xs font-black uppercase tracking-wider text-slate-600">Sales & Debt</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('sales.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="receipt" class="w-5 h-5 {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Sales Invoices</span>
                        </a>
                        <a href="{{ route('payments.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('payments.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="wallet" class="w-5 h-5 {{ request()->routeIs('payments.*') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Payments & Debt</span>
                        </a>
                    </div>
                </div>

                <!-- Catalog & Inventory -->
                <div>
                    <span class="px-3 text-xs font-black uppercase tracking-wider text-slate-600">Inventory Catalog</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('products.index') }}" 
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('products.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <div class="flex items-center gap-3">
                                <i data-lucide="tag" class="w-5 h-5 {{ request()->routeIs('products.*') ? 'text-white' : 'text-slate-700' }}"></i>
                                <span>Products & Prices</span>
                            </div>
                        </a>
                        <a href="{{ route('purchases.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('purchases.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="truck" class="w-5 h-5 {{ request()->routeIs('purchases.*') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Stock In (Purchases)</span>
                        </a>
                        <a href="{{ route('categories.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('categories.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="layers" class="w-5 h-5 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Categories</span>
                        </a>
                        <a href="{{ route('stock-transactions.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('stock-transactions.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="arrow-left-right" class="w-5 h-5 {{ request()->routeIs('stock-transactions.*') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Stock Movement Log</span>
                        </a>
                    </div>
                </div>

                <!-- Stakeholders -->
                <div>
                    <span class="px-3 text-xs font-black uppercase tracking-wider text-slate-600">People & Accounts</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('customers.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('customers.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="users" class="w-5 h-5 {{ request()->routeIs('customers.*') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Customers</span>
                        </a>
                        <a href="{{ route('suppliers.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('suppliers.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="building-2" class="w-5 h-5 {{ request()->routeIs('suppliers.*') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Suppliers</span>
                        </a>
                    </div>
                </div>

                <!-- Reports & Security -->
                <div>
                    <span class="px-3 text-xs font-black uppercase tracking-wider text-slate-600">Business Reports</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('reports.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('reports.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="bar-chart-3" class="w-5 h-5 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Finance Reports</span>
                        </a>
                        <a href="{{ route('audit-logs.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-black transition-all {{ request()->routeIs('audit-logs.*') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-900 hover:bg-slate-100 hover:text-emerald-700' }}">
                            <i data-lucide="shield-check" class="w-5 h-5 {{ request()->routeIs('audit-logs.*') ? 'text-white' : 'text-slate-700' }}"></i>
                            <span>Audit Trail</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Footer Profile Card -->
            <div class="p-4 border-t-2 border-slate-100 bg-slate-50">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-white border border-slate-200">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center justify-center font-black text-sm">
                        {{ substr(auth()->user()->name ?? 'Admin', 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-slate-900 truncate">{{ auth()->user()->name ?? 'Cashier / Admin' }}</p>
                        <p class="text-[11px] text-emerald-800 font-black truncate">{{ auth()->user()->role ?? 'CASHIER' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Sign Out" class="text-slate-600 hover:text-red-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-8 shrink-0 z-10">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
                        <span class="font-bold text-slate-800">Merkato</span>
                        <span class="text-slate-300">/</span>
                        <span class="font-medium text-slate-600">{{ $headerTitle ?? 'Cashier Portal' }}</span>
                        @if(isset($headerSubtitle))
                            <span class="text-slate-300">/</span>
                            <span class="text-slate-500">{{ $headerSubtitle }}</span>
                        @endif
                    </div>
                </div>

                <!-- Header Actions -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Open Cashier POS Quick Button -->
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-emerald-600/25 hover:shadow-lg transition-all">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                        <span>Cashier POS</span>
                    </a>

                    <!-- Quick Stock-In Button -->
                    <a href="{{ route('purchases.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold shadow-sm transition-all">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Stock-In</span>
                    </a>

                    <!-- Low Stock Alert -->
                    <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" 
                       class="relative p-2 text-slate-500 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition-colors"
                       title="{{ $lowStockCount ?? 0 }} Low Stock Alert Items">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        @if(($lowStockCount ?? 0) > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white ring-2 ring-white animate-pulse">
                                {{ $lowStockCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </header>

            <!-- Flash Notification Alerts -->
            @if(session('success'))
                <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-xs">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 shadow-xs">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Main Dynamic Body Slot -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
