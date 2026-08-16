<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'login:'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            AuditLog::record(null, 'login_rate_limited', 'auth');

            return back()->withErrors(['email' => 'Too many login attempts. Please wait a moment and try again.']);
        }
        RateLimiter::hit($throttleKey, 60); // 5 attempts / 60 seconds

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Invalid email or password.']);
        }

        if ($user->isLocked()) {
            AuditLog::record($user->id, 'login_blocked_locked', 'auth', $user->id);

            return back()->withErrors(['email' => 'Your account is temporarily locked due to repeated failed attempts. Try again later.']);
        }

        if (! Hash::check($validated['password'], $user->password)) {
            $user->registerFailedLogin();

            return back()->withErrors(['email' => 'Invalid email or password.']);
        }

        if (! $user->email_verified_at) {
            return back()->withErrors(['email' => 'Please verify your email address before logging in.']);
        }

        if (! $user->isActive()) {
            return back()->withErrors(['email' => 'Your account is not active. Please contact support.']);
        }

        $user->clearFailedLogins();
        RateLimiter::clear($throttleKey);

        if ($user->totp_enabled && $user->totp_secret) {
            // Don't log the user in yet — stash a short-lived pending
            // identifier and force the 2FA challenge first.
            $request->session()->put('2fa_pending_user_id', $user->id);
            $request->session()->put('2fa_remember', $request->boolean('remember'));

            return redirect()->route('two-factor.show');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        AuditLog::record($user->id, 'login', 'auth', $user->id);

        return redirect()->intended($this->defaultRedirectFor($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userId) {
            AuditLog::record($userId, 'logout', 'auth', $userId);
        }

        return redirect()->route('login');
    }

    /** Where a user lands after login depends on their role — not everyone is an admin. */
    public function defaultRedirectFor(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }
        if ($user->hasRole('vendor')) {
            return route('vendor.dashboard');
        }

        return route('home');
    }
}
