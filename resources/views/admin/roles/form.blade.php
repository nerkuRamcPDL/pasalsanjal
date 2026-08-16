<x-layouts.admin>
    <h1 class="h4 fw-bold mb-4">{{ $role ? 'Edit Role' : 'Create Role' }}</h1>

    <form method="POST" action="{{ $role ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
        @csrf
        @if ($role) @method('PUT') @endif

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $role->name ?? '') }}"
                            {{ $role && $role->is_system ? 'readonly' : '' }} required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="{{ old('description', $role->description ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Permissions</div>
            <div class="card-body">
                @foreach ($groupedPermissions as $module => $permissions)
                    <div class="mb-3">
                        <div class="fw-semibold text-capitalize small text-muted mb-2">{{ str_replace('_', ' ', $module) }}</div>
                        <div class="row">
                            @foreach ($permissions as $permission)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permission->id }}" id="perm-{{ $permission->id }}"
                                            {{ in_array($permission->id, $selectedPermissionIds, true) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="perm-{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">{{ $role ? 'Update Role' : 'Create Role' }}</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</x-layouts.admin>
