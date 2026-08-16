<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function __construct(private readonly TotpService $totp) {}

    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('2fa_pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('2fa_pending_user_id');
        if (! $userId) {
            return redirect()->route('login');
        }

        $throttleKey = 'otp-verify:'.$userId;
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            AuditLog::record($userId, 'otp_rate_limited', 'auth', $userId);

            return back()->withErrors(['code' => 'Too many attempts. Please wait a moment and try again.']);
        }
        RateLimiter::hit($throttleKey, 300); // 5 attempts / 5 minutes

        $validated = $request->validate(['code' => ['required', 'string']]);

        $user = User::find($userId);
        if (! $user || ! $user->totp_secret || ! $this->totp->verify($user->totp_secret, $validated['code'])) {
            AuditLog::record($userId, 'otp_verification_failed', 'auth', $userId);

            return back()->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        RateLimiter::clear($throttleKey);

        $remember = (bool) $request->session()->pull('2fa_remember', false);
        $request->session()->forget('2fa_pending_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        AuditLog::record($user->id, 'login', 'auth', $user->id);

        return redirect()->intended(
            app(AuthenticatedSessionController::class)->defaultRedirectFor($user)
        );
    }
}
