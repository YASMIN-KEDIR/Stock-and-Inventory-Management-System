<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} | {{ config('app.name', 'Bedewi Sales') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Local High-Speed Scripts -->
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="{{ asset('js/alpine.min.js') }}"></script>
    <script src="{{ asset('js/lucide.min.js') }}"></script>
    <script src="{{ asset('js/chart.min.js') }}"></script>
</head>
<body class="min-h-full font-sans antialiased text-slate-800 bg-slate-100/60 selection:bg-indigo-500 selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen">

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

        <!-- Sidebar Navigation - Fixed & Stationary (Never Moves When Page Scrolls) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="app-sidebar fixed inset-y-0 left-0 z-50 w-64 flex flex-col transition-transform duration-300 ease-in-out shadow-2xl print:hidden">
            
            <!-- App Brand / Logo -->
            <div class="app-sidebar-header shrink-0 flex items-center justify-between px-5 py-4">
                <a href="{{ route('dashboard') }}" class="group">
                    <x-logo size="md" variant="light" />
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-300 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 cursor-pointer">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation: Core Business Sections (Zero Gaps, Clean & Direct) -->
            <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-1.5 text-xs">
                
                <span class="app-sidebar-section-title px-2 text-[10px] tracking-widest text-slate-400 uppercase font-black mb-1.5">Main Menu</span>

                <!-- 1. DASHBOARD -->
                <div>
                    <a href="{{ route('dashboard') }}" 
                       class="app-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-extrabold text-sm block truncate">1. Dashboard</span>
                        </div>
                    </a>
                </div>

                <!-- 2. PRODUCTS (Clean single block, no nested tags, no gap) -->
                <div>
                    <a href="{{ route('products.index') }}" 
                       class="app-nav-link {{ (request()->routeIs('products.*') || request()->routeIs('categories.*')) ? 'active' : '' }}">
                        <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-extrabold text-sm block truncate">2. Products</span>
                        </div>
                    </a>
                </div>

                <!-- 3. USERS (Customers & Suppliers) -->
                <div x-data="{ usersOpen: {{ (request()->routeIs('users.*') || request()->routeIs('customers.*') || request()->routeIs('suppliers.*')) ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" 
                            @click="usersOpen = !usersOpen" 
                            class="app-nav-link w-full text-left flex items-center justify-between cursor-pointer mb-0 {{ (request()->routeIs('users.*') || request()->routeIs('customers.*') || request()->routeIs('suppliers.*')) ? 'active' : '' }}">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-7 h-7 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center shrink-0">
                                <i data-lucide="users" class="w-4 h-4"></i>
                            </div>
                            <span class="font-extrabold text-sm block truncate">3. Users</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="usersOpen ? 'rotate-180 text-white' : ''"></i>
                    </button>

                    <!-- Customers & Suppliers Submenu -->
                    <div x-show="usersOpen" x-cloak class="pl-3.5 pr-1 space-y-1 pt-1 pb-1">
                        <a href="{{ route('customers.index') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('customers.*') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-800/60 text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-sky-400 shrink-0"></i>
                            <span class="truncate">Customers</span>
                        </a>

                        <a href="{{ route('suppliers.index') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('suppliers.*') ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-800/60 text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="building-2" class="w-3.5 h-3.5 text-teal-400 shrink-0"></i>
                            <span class="truncate">Suppliers</span>
                        </a>
                    </div>
                </div>

                <!-- 4. SELL SECTION -->
                <div>
                    <a href="{{ route('sales.create') }}" 
                       class="app-nav-link {{ request()->routeIs('sales.create') || request()->routeIs('sell.pos') ? 'active' : '' }}">
                        <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 flex items-center justify-center shrink-0">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-extrabold text-sm block truncate">4. Sell Items</span>
                        </div>
                    </a>
                </div>

                <!-- 5. INFO SECTION (Stock Information, Valuation & Sales Invoices) -->
                <div x-data="{ infoOpen: {{ (request()->routeIs('info.*') || request()->routeIs('stock-transactions.*') || (request()->routeIs('sales.*') && !request()->routeIs('sales.create'))) ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" 
                            @click="infoOpen = !infoOpen" 
                            class="app-nav-link w-full text-left flex items-center justify-between cursor-pointer mb-0 {{ (request()->routeIs('info.*') || request()->routeIs('stock-transactions.*') || (request()->routeIs('sales.*') && !request()->routeIs('sales.create'))) ? 'active' : '' }}">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                                <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                            </div>
                            <span class="font-extrabold text-sm block truncate">5. Stock & Info</span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="infoOpen ? 'rotate-180 text-white' : ''"></i>
                    </button>

                    <!-- Info Sub-Links -->
                    <div x-show="infoOpen" x-cloak class="pl-3.5 pr-1 space-y-1 pt-1 pb-1">
                        <a href="{{ route('info.index', ['tab' => 'stock']) }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ ((request('tab') === 'stock' && request()->routeIs('info.*')) || request()->routeIs('stock-transactions.*')) ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-800/60 text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="pie-chart" class="w-3.5 h-3.5 text-cyan-400 shrink-0"></i>
                            <span class="truncate">Stock Information</span>
                        </a>

                        <a href="{{ route('info.index', ['tab' => 'valuation']) }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ (request('tab', 'valuation') === 'valuation' && request()->routeIs('info.*')) ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-800/60 text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="coins" class="w-3.5 h-3.5 text-amber-400 shrink-0"></i>
                            <span class="truncate">Stock Value & Prices</span>
                        </a>

                        <a href="{{ route('info.index', ['tab' => 'invoices']) }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition-all {{ ((request('tab') === 'invoices' && request()->routeIs('info.*')) || (request()->routeIs('sales.*') && !request()->routeIs('sales.create'))) ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-800/60 text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i data-lucide="receipt" class="w-3.5 h-3.5 text-emerald-400 shrink-0"></i>
                            <span class="truncate">Sales Invoices</span>
                        </a>
                    </div>
                </div>

                <!-- 6. FINANCIAL REPORT -->
                <div>
                    <a href="{{ route('reports.financial') }}" 
                       class="app-nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <div class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center shrink-0">
                            <i data-lucide="receipt" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-extrabold text-sm block truncate">6. Financial Report</span>
                        </div>
                    </a>
                </div>

            </nav>

            <!-- User Footer Profile Card -->
            <div class="app-sidebar-footer shrink-0 p-4">
                <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/90 border border-slate-800">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-xs">
                        {{ substr(auth()->user()->name ?? 'A', 0, 2) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-[10px] text-slate-300 truncate font-semibold">{{ auth()->user()->role ?? 'CASHIER' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" title="Sign Out" class="text-slate-300 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 lg:pl-64 print:pl-0">

            <!-- Top Header Bar (Fixed / Sticky at Top) -->
            <header class="sticky top-0 z-30 h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-8 shrink-0 shadow-xs print:hidden">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-lg hover:bg-slate-100 cursor-pointer">
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

                <!-- Header Right Actions: Add Product & Quick Sell -->
                <div class="flex items-center gap-2.5">
                    <!-- Direct Add Product Button -->
                    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-xs transition-all cursor-pointer">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>+ Add Product</span>
                    </a>

                    <!-- Quick Sell (POS) Button -->
                    <a href="{{ route('sales.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-xs transition-all cursor-pointer">
                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                        <span>Sell (POS)</span>
                    </a>

                    <!-- Low Stock Alert Icon -->
                    <a href="{{ route('info.index', ['tab' => 'valuation', 'status' => 'low_stock']) }}" 
                       class="relative p-2 text-slate-500 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition-colors"
                       title="Low Stock Items">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                    </a>
                </div>
            </header>

            <!-- Flash Notification Alerts -->
            @if(session('success'))
                <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-xl bg-emerald-50 border-2 border-emerald-400 text-emerald-950 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-700 shrink-0"></i>
                        <p class="text-sm font-bold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-xl bg-rose-50 border-2 border-rose-400 text-rose-950 flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-rose-700 shrink-0"></i>
                        <p class="text-sm font-bold">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-4 sm:mx-8 mt-4 p-4 rounded-xl bg-rose-50 border-2 border-rose-300 text-rose-950 shadow-xs">
                    <div class="flex items-center gap-2 mb-2 font-bold text-sm text-rose-800">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        <span>Please fix the following issues:</span>
                    </div>
                    <ul class="list-disc pl-5 text-xs font-semibold space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Dynamic Body Slot -->
            <main class="flex-1 p-4 sm:p-8">
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
