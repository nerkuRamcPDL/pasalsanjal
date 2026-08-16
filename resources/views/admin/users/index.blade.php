<x-layouts.admin>
    <h1 class="h4 fw-bold mb-4">Users</h1>

    <form method="GET" class="mb-3" style="max-width: 320px;">
        <input type="search" name="search" class="form-control" placeholder="Search by name or email" value="{{ $search }}">
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Email</th><th>Type</th><th>Status</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="text-capitalize">{{ $user->user_type }}</td>
                            <td>
                                @php $colors = ['active' => 'success', 'suspended' => 'warning', 'blocked' => 'danger', 'pending' => 'secondary']; @endphp
                                <span class="badge bg-{{ $colors[$user->status] ?? 'secondary' }} text-capitalize">{{ $user->status }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Roles</a>
                                @if ($user->status === 'active')
                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-warning">Suspend</button>
                                    </form>
                                @elseif ($user->status === 'suspended')
                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success">Reactivate</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
</x-layouts.admin>
