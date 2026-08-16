<x-layouts.admin>
    <h1 class="h4 fw-bold mb-1">Edit User</h1>
    <p class="text-muted small mb-4">{{ $user->name }} — {{ $user->email }}</p>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="card mb-3">
            <div class="card-header">Assigned Roles</div>
            <div class="card-body">
                @foreach ($roles as $role)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}"
                            id="role-{{ $role->id }}" {{ in_array($role->id, $userRoleIds, true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="role-{{ $role->id }}">
                            {{ $role->name }}
                            @if ($role->description)
                                <span class="text-muted small d-block">{{ $role->description }}</span>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Roles</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</x-layouts.admin>
