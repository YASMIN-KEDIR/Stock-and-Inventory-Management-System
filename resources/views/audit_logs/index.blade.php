<x-layouts.app>
    <x-slot:title>Audit Trail</x-slot:title>
    <x-slot:headerTitle>Security & Governance</x-slot:headerTitle>
    <x-slot:headerSubtitle>System Mutation & Activity Audit Log</x-slot:headerSubtitle>

    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="glass-card rounded-3xl p-5 border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('audit-logs.index') }}" class="flex flex-wrap items-center gap-4">
                <input type="text" name="action" value="{{ request('action') }}" placeholder="Filter by action (e.g. PRODUCT_CREATE)..." class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                <input type="text" name="entity_type" value="{{ request('entity_type') }}" placeholder="Entity (e.g. Sale, Product)..." class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all">Filter Logs</button>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="glass-card rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100/70 text-slate-500 text-[11px] uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="px-6 py-4">Timestamp</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Action</th>
                            <th class="px-6 py-4">Entity</th>
                            <th class="px-6 py-4">Client IP</th>
                            <th class="px-6 py-4 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-xs">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 font-sans font-medium text-slate-900">
                                    {{ $log->user->name ?? 'System' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-indigo-600">
                                    {{ $log->action }}
                                </td>
                                <td class="px-6 py-4 text-slate-700">
                                    {{ $log->entity_type }} #{{ $log->entity_id ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                                <td class="px-6 py-4 text-right font-sans">
                                    <span class="text-[11px] text-slate-400 truncate max-w-xs block">
                                        {{ $log->new_values ? json_encode($log->new_values) : '—' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 font-sans">
                                    No audit logs recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 font-sans">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
