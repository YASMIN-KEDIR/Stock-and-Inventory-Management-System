<x-layouts.app>
    <x-slot:title>Customers</x-slot:title>
    <x-slot:headerTitle>Commercial Relations</x-slot:headerTitle>
    <x-slot:headerSubtitle>Customer Directory & Credit Balances</x-slot:headerSubtitle>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <form method="GET" action="{{ route('customers.index') }}" class="flex-1 max-w-md">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer by name or phone..." class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </form>

            <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-xs">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Add Customer</span>
            </a>
        </div>

        <div class="glass-card rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-6 py-4">Customer Name</th>
                            <th class="px-6 py-4">Phone & Email</th>
                            <th class="px-6 py-4 text-right">Total Invoiced</th>
                            <th class="px-6 py-4 text-right">Total Paid</th>
                            <th class="px-6 py-4 text-right">Outstanding Debt</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('customers.show', $customer) }}" class="font-bold text-slate-900 hover:text-indigo-600 block transition-colors">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-600">
                                    <div>{{ $customer->phone ?? '—' }}</div>
                                    <div class="text-slate-400">{{ $customer->email ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-700">
                                    ${{ number_format($customer->total_sales, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-emerald-600">
                                    ${{ number_format($customer->total_paid, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($customer->outstanding_balance > 0)
                                        <span class="font-bold text-amber-600">${{ number_format($customer->outstanding_balance, 2) }}</span>
                                    @else
                                        <span class="text-xs font-semibold text-emerald-600">Clear ($0.00)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('customers.show', $customer) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-colors" title="View Statement">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 rounded-lg hover:bg-slate-100 transition-colors" title="Edit Profile">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    No customers registered.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
