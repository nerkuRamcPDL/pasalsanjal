<x-layouts.admin>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">Roles</h1>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">+ New Role</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Slug</th><th>Users</th><th>Type</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td class="fw-semibold">{{ $role->name }}</td>
                            <td class="text-muted"><code>{{ $role->slug }}</code></td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                @if ($role->is_system)
                                    <span class="badge bg-secondary">System</span>
                                @else
                                    <span class="badge bg-light text-dark">Custom</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                @if (! $role->is_system)
                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('Delete this role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
