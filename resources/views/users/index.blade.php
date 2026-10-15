<x-layouts.app>
    <x-slot:title>Users &bull; Customers & Suppliers Hub</x-slot:title>
    <x-slot:headerTitle>Users Management</x-slot:headerTitle>
    <x-slot:headerSubtitle>
        {{ $tab === 'suppliers' ? 'Suppliers Directory & Accounts Payable' : 'Customers Directory & Credit Accounts' }}
    </x-slot:headerSubtitle>

    <div class="space-y-6">

        <!-- Top Header & Tabs -->
        <div class="glass-card rounded-3xl p-5 border border-slate-200/80 bg-white shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-sky-50 text-sky-800 border border-sky-300">
                        <i data-lucide="users" class="w-3.5 h-3.5 text-sky-600"></i>
                        <span>Users &bull; Customers and Suppliers</span>
                    </span>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1.5">
                        {{ $tab === 'suppliers' ? 'Suppliers List' : 'Customers List' }}
                    </h1>
                    <p class="text-xs text-slate-600 font-semibold mt-0.5">
                        {{ $tab === 'suppliers' ? 'View supplier contact information and track money owed.' : 'View customer details, sales history, and unpaid credit balances.' }}
                    </p>
                </div>

                <!-- Switcher Tabs & Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shrink-0">
                        <a href="{{ route('users.index', ['tab' => 'customers']) }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black transition-all {{ $tab === 'customers' ? 'bg-white text-indigo-700 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900' }}">
                            <i data-lucide="user-check" class="w-4 h-4 text-indigo-600"></i>
                            <span>Customers ({{ $totalCustomersCount }})</span>
                        </a>

                        <a href="{{ route('users.index', ['tab' => 'suppliers']) }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-black transition-all {{ $tab === 'suppliers' ? 'bg-white text-teal-700 shadow-xs border border-slate-200' : 'text-slate-600 hover:text-slate-900' }}">
                            <i data-lucide="building-2" class="w-4 h-4 text-teal-600"></i>
                            <span>Suppliers ({{ $totalSuppliersCount }})</span>
                        </a>
                    </div>

                    @if($tab === 'customers')
                        <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-xs transition-all">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span>+ Add Customer</span>
                        </a>
                    @else
                        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black shadow-xs transition-all">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            <span>+ Add Supplier</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Metric Cards (Direct & Clear) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Customers Count -->
            <div class="glass-card rounded-2xl p-4 border border-indigo-200 bg-white shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-900">Total Customers</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalCustomersCount) }}</p>
                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Active buyers</p>
            </div>

            <!-- 2. Customer Debt -->
            <div class="glass-card rounded-2xl p-4 border border-amber-200 bg-amber-50/30 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-900">Customer Debt (Unpaid)</span>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ number_format($totalCustomerDebt, 2) }} <span class="text-xs font-bold text-slate-500">Birr</span></p>
                <p class="text-[11px] font-semibold text-amber-800 mt-0.5">{{ $customersWithDebtCount }} customers have unpaid credit</p>
            </div>

            <!-- 3. Suppliers Count -->
            <div class="glass-card rounded-2xl p-4 border border-teal-200 bg-white shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-900">Total Suppliers</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($totalSuppliersCount) }}</p>
                <p class="text-[11px] font-semibold text-slate-500 mt-0.5">Registered vendors</p>
            </div>

            <!-- 4. Supplier Payables -->
            <div class="glass-card rounded-2xl p-4 border border-rose-200 bg-rose-50/30 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-900">Money Owed to Suppliers</span>
                <p class="text-2xl font-black text-rose-700 mt-1">{{ number_format($totalSupplierDebt, 2) }} <span class="text-xs font-bold text-slate-500">Birr</span></p>
                <p class="text-[11px] font-semibold text-rose-800 mt-0.5">{{ $suppliersWithPayablesCount }} suppliers with unpaid balance</p>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB: CUSTOMERS DIRECTORY                                                  --}}
        {{-- ========================================================================= --}}
        @if($tab === 'customers')
            <!-- Search & Filter Bar -->
            <div class="glass-card rounded-2xl p-4 border border-slate-200/80 bg-white shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <form method="GET" action="{{ route('users.index') }}" class="flex-1 max-w-md">
                    <input type="hidden" name="tab" value="customers">
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                        <input type="text" name="customer_search" value="{{ request('customer_search') }}" placeholder="Search customer by name, phone, or email..." 
                               class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                    </div>
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        <span>Register Customer</span>
                    </a>
                </div>
            </div>

            <!-- Customers Table -->
            <div class="glass-card rounded-3xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Customers Directory</h2>
                        <p class="text-xs text-slate-500 font-semibold">All registered clients with contact numbers, transaction history, and credit records.</p>
                    </div>
                    <span class="text-xs font-black px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $customers->total() }} Customers
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 uppercase tracking-wider font-extrabold border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5">Customer Name</th>
                                <th class="px-5 py-3.5">Phone & Email</th>
                                <th class="px-5 py-3.5 text-center">Invoices</th>
                                <th class="px-5 py-3.5 text-right">Total Invoiced (Birr)</th>
                                <th class="px-5 py-3.5 text-right">Total Paid (Birr)</th>
                                <th class="px-5 py-3.5 text-right">Remaining Debt</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80">
                            @forelse($customers as $customer)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('customers.show', $customer) }}" class="font-extrabold text-slate-900 hover:text-indigo-600 block">
                                            {{ $customer->name }}
                                        </a>
                                        <span class="text-[11px] text-slate-400 font-semibold">{{ $customer->address ?? 'No physical address' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-700 font-bold">
                                        <div>{{ $customer->phone ?? '—' }}</div>
                                        <div class="text-slate-400 font-normal text-[11px]">{{ $customer->email ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center font-bold text-slate-700">
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200">{{ $customer->sales_count }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-black text-slate-900">
                                        {{ number_format($customer->total_sales, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-bold text-emerald-700">
                                        {{ number_format($customer->total_paid, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-black">
                                        @if($customer->outstanding_balance > 0)
                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-amber-800 bg-amber-50 border border-amber-200 font-bold">
                                                {{ number_format($customer->outstanding_balance, 2) }} Birr
                                            </span>
                                        @else
                                            <span class="text-emerald-600 font-bold">No Debt (0.00 Birr)</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('customers.show', $customer) }}" class="p-1 rounded text-slate-600 hover:text-indigo-600 hover:bg-slate-100" title="View Profile">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('customers.edit', $customer) }}" class="p-1 rounded text-slate-600 hover:text-indigo-600 hover:bg-slate-100" title="Edit Customer">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-slate-500 font-bold">
                                        No customers registered yet. Click "+ Add Customer" above to register one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>

        {{-- ========================================================================= --}}
        {{-- TAB: SUPPLIERS DIRECTORY                                                  --}}
        {{-- ========================================================================= --}}
        @elseif($tab === 'suppliers')
            <!-- Search & Filter Bar -->
            <div class="glass-card rounded-2xl p-4 border border-slate-200/80 bg-white shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <form method="GET" action="{{ route('users.index') }}" class="flex-1 max-w-md">
                    <input type="hidden" name="tab" value="suppliers">
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                        <input type="text" name="supplier_search" value="{{ request('supplier_search') }}" placeholder="Search supplier or company name..." 
                               class="w-full pl-9 pr-3 py-2 rounded-xl bg-slate-50 border-2 border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:border-indigo-600 focus:outline-none">
                    </div>
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-xs">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Register Supplier</span>
                    </a>
                </div>
            </div>

            <!-- Suppliers Table -->
            <div class="glass-card rounded-3xl overflow-hidden border-2 border-slate-200 bg-white shadow-xs">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Suppliers & Vendors Directory</h2>
                        <p class="text-xs text-slate-500 font-semibold">All registered suppliers with contact information, purchases ledger, and payable debts.</p>
                    </div>
                    <span class="text-xs font-black px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $suppliers->total() }} Suppliers
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 uppercase tracking-wider font-extrabold border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3.5">Supplier / Contact</th>
                                <th class="px-5 py-3.5">Company Name</th>
                                <th class="px-5 py-3.5">Phone & Email</th>
                                <th class="px-5 py-3.5 text-center">Purchases</th>
                                <th class="px-5 py-3.5 text-right">Total Purchased (Birr)</th>
                                <th class="px-5 py-3.5 text-right">Money Owed</th>
                                <th class="px-5 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200/80">
                            @forelse($suppliers as $supplier)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="font-extrabold text-slate-900 hover:text-teal-600 block">
                                            {{ $supplier->name }}
                                        </a>
                                        <span class="text-[11px] text-slate-400 font-semibold">{{ $supplier->address ?? 'No physical address' }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-800 font-bold">
                                        {{ $supplier->company_name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-700 font-bold">
                                        <div>{{ $supplier->phone }}</div>
                                        <div class="text-slate-400 font-normal text-[11px]">{{ $supplier->email ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center font-bold text-slate-700">
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200">{{ $supplier->purchases_count }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-black text-slate-900">
                                        {{ number_format($supplier->total_purchased, 2) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-black">
                                        @if($supplier->outstanding_balance > 0)
                                            <span class="inline-flex px-2 py-0.5 rounded-lg text-rose-800 bg-rose-50 border border-rose-200 font-bold">
                                                {{ number_format($supplier->outstanding_balance, 2) }} Birr
                                            </span>
                                        @else
                                            <span class="text-emerald-600 font-bold">All Paid (0.00 Birr)</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="p-1 rounded text-slate-600 hover:text-teal-600 hover:bg-slate-100" title="View Profile">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="p-1 rounded text-slate-600 hover:text-teal-600 hover:bg-slate-100" title="Edit Supplier">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-slate-500 font-bold">
                                        No suppliers registered yet. Click "+ Add Supplier" above to register one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($suppliers->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-layouts.app>
