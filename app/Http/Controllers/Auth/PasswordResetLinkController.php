<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $throttleKey = 'password-reset:'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            return back()->withErrors(['email' => 'Too many reset requests. Please try again later.']);
        }
        RateLimiter::hit($throttleKey, 900); // 3 attempts / 15 minutes

        $user = User::where('email', $request->email)->first();

        // Always show the same message regardless of whether the account
        // exists — prevents user enumeration via this endpoint.
        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => hash('sha256', $token), 'created_at' => now()]
            );

            Log::info('Password reset link queued', [
                'email' => $user->email,
                'link' => route('password.reset', ['token' => $token, 'email' => $user->email]),
            ]);
        }

        return back()->with('flash_success', 'If an account exists for that email, a password reset link has been sent.');
    }
}
