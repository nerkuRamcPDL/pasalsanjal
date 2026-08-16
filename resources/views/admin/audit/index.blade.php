<x-layouts.admin>
    <h1 class="h4 fw-bold mb-4">Audit Logs</h1>
    <p class="text-muted small mb-3">Append-only record of security-sensitive actions. This log is never edited or deleted by application code.</p>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>User</th><th>Action</th><th>Module</th><th>Record</th><th>IP Address</th><th>When</th></tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->user->name ?? 'System' }}</td>
                            <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                            <td>{{ $log->module }}</td>
                            <td class="text-muted small">{{ $log->record_id ?? '—' }}</td>
                            <td class="text-muted small">{{ $log->ip_address }}</td>
                            <td class="text-muted small">{{ $log->created_at?->format('M j, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No audit entries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $logs->links() }}</div>
</x-layouts.admin>
