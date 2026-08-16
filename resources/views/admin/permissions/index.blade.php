<x-layouts.admin>
    <h1 class="h4 fw-bold mb-4">Permissions</h1>
    <p class="text-muted small mb-4">Permissions are grouped by module and assigned to roles from the Roles screen — they aren't edited individually.</p>

    @foreach ($groupedPermissions as $module => $permissions)
        <div class="card mb-3">
            <div class="card-header text-capitalize">{{ str_replace('_', ' ', $module) }}</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td class="fw-semibold">{{ $permission->name }}</td>
                                <td class="text-muted"><code>{{ $permission->slug }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</x-layouts.admin>
