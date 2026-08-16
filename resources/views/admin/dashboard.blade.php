<x-layouts.admin>
    <h1 class="h4 fw-bold mb-4">Dashboard</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted small">Total Users</div>
                <div class="fs-3 fw-bold">{{ number_format($kpis['total_users']) }}</div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted small">Total Vendors</div>
                <div class="fs-3 fw-bold">{{ number_format($kpis['total_vendors']) }}</div>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card"><div class="card-body">
                <div class="text-muted small">Pending Vendor Approvals</div>
                <div class="fs-3 fw-bold" style="color: var(--marigold-600);">{{ number_format($kpis['pending_vendors']) }}</div>
            </div></div>
        </div>
    </div>

    <div class="alert alert-info small">
        This is the Phase 1 foundation dashboard. Sales, order, and commission KPIs will populate once the Vendor, Catalog, and Orders modules are built in later phases.
    </div>

    <div class="card">
        <div class="card-header">Recent Activity (Audit Log)</div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>User</th><th>Action</th><th>Module</th><th>IP</th><th>When</th></tr>
                </thead>
                <tbody>
                    @forelse ($recentAudit as $log)
                        <tr>
                            <td>{{ $log->user->name ?? 'System' }}</td>
                            <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                            <td>{{ $log->module }}</td>
                            <td class="text-muted small">{{ $log->ip_address }}</td>
                            <td class="text-muted small">{{ $log->created_at?->format('M j, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No activity recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
