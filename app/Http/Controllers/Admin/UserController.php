<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->query('search', '');

        $users = User::query()
            ->when($search !== '', fn ($q) => $q->where(fn ($q2) => $q2
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
            'userRoleIds' => $user->roles()->pluck('roles.id')->all(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->roles()->sync($validated['roles'] ?? []);

        AuditLog::record(Auth::id(), 'update_roles', 'customers', $user->id, null, ['roles' => $validated['roles'] ?? []]);

        return redirect()->route('admin.users.index')->with('flash_success', 'User updated.');
    }

    public function suspend(User $user): RedirectResponse
    {
        $this->setStatus($user, 'suspended');

        return redirect()->route('admin.users.index');
    }

    public function activate(User $user): RedirectResponse
    {
        $this->setStatus($user, 'active');

        return redirect()->route('admin.users.index');
    }

    private function setStatus(User $user, string $status): void
    {
        $old = $user->status;
        $user->update(['status' => $status]);

        AuditLog::record(Auth::id(), 'status_change', 'customers', $user->id, ['status' => $old], ['status' => $status]);
    }
}
