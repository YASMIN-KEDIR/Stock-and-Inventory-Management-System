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

    <!-- Local High-Speed Scripts -->
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <script src="{{ asset('js/lucide.min.js') }}"></script>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100/60 selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false }">
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

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:h-screen lg:shrink-0 shadow-xl">
            
            <!-- App Brand / Logo -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800 bg-slate-950/40">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                        <i data-lucide="boxes" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <span class="text-lg font-black tracking-tight text-white block">Merkato<span class="text-emerald-400">.</span></span>
                        <span class="text-[10px] font-bold tracking-wider text-slate-400 uppercase">Stock & Inventory</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3.5 py-5 overflow-y-auto space-y-6 text-xs">
                
                <!-- Main Operations -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Main Menu</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span class="text-sm font-semibold">Dashboard</span>
                        </a>
                        <a href="{{ route('sales.create') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('sales.create') ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/20' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                            <span class="text-sm font-semibold">Point of Sale (POS)</span>
                        </a>
                    </div>
                </div>

                <!-- Sales & Finance -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Sales & Debt</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('sales.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('sales.*') && !request()->routeIs('sales.create') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="receipt" class="w-4 h-4"></i>
                            <span class="text-sm">Sales Invoices</span>
                        </a>
                        <a href="{{ route('payments.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('payments.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="wallet" class="w-4 h-4"></i>
                            <span class="text-sm">Payments & Credit</span>
                        </a>
                    </div>
                </div>

                <!-- Inventory Management -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Inventory</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('products.index') }}" 
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('products.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <i data-lucide="package" class="w-4 h-4"></i>
                                <span class="text-sm">Products</span>
                            </div>
                        </a>
                        <a href="{{ route('purchases.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('purchases.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="truck" class="w-4 h-4"></i>
                            <span class="text-sm">Stock In (Purchases)</span>
                        </a>
                        <a href="{{ route('stock-transactions.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('stock-transactions.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                            <span class="text-sm">Stock Movements</span>
                        </a>
                        <a href="{{ route('categories.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('categories.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                            <span class="text-sm">Categories</span>
                        </a>
                    </div>
                </div>

                <!-- Contacts -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">People</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('customers.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('customers.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <span class="text-sm">Customers</span>
                        </a>
                        <a href="{{ route('suppliers.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('suppliers.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="building" class="w-4 h-4"></i>
                            <span class="text-sm">Suppliers</span>
                        </a>
                    </div>
                </div>

                <!-- Reports & System -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400">Analytics</span>
                    <div class="mt-2 space-y-1">
                        <a href="{{ route('reports.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('reports.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                            <span class="text-sm">Financial Reports</span>
                        </a>
                        <a href="{{ route('audit-logs.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition-all {{ request()->routeIs('audit-logs.*') ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            <span class="text-sm">Audit Trail</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Footer Profile Card -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900 border border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-xs">
                        {{ substr(auth()->user()->name ?? 'A', 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->role ?? 'CASHIER' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Sign Out" class="text-slate-400 hover:text-rose-400 p-1 rounded-lg hover:bg-slate-800 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-8 shrink-0 z-10 shadow-xs">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div class="hidden sm:flex items-center gap-2 text-sm text-slate-500">
                        <span class="font-bold text-slate-800">Merkato</span>
                        <span class="text-slate-300">/</span>
                        <span class="font-medium text-slate-600">{{ $headerTitle ?? 'Store Overview' }}</span>
                        @if(isset($headerSubtitle))
                            <span class="text-slate-300">/</span>
                            <span class="text-slate-500">{{ $headerSubtitle }}</span>
                        @endif
                    </div>
                </div>

                <!-- Header Right Actions -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>New Sale</span>
                    </a>

                    <!-- Low Stock Alert Icon -->
                    <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" 
                       class="relative p-2 text-slate-500 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition-colors"
                       title="Low Stock Items">
                        <i data-lucide="bell" class="w-5 h-5"></i>
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
