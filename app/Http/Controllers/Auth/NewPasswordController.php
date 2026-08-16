<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'token' => $request->query('token', ''),
            'email' => $request->query('email', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required', 'confirmed',
                'min:'.config('security.password_min_length', 10),
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must include an uppercase letter, a lowercase letter, a number, and a symbol.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();

        if (! $record || ! hash_equals($record->token, hash('sha256', $validated['token']))) {
            return back()->withErrors(['email' => 'This reset link is invalid or has expired.']);
        }

        $user = User::where('email', $validated['email'])->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Account not found.']);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ])->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        AuditLog::record($user->id, 'password_reset', 'auth', $user->id);

        return redirect()->route('login')->with('flash_success', 'Password reset successfully. Please log in.');
    }
}
