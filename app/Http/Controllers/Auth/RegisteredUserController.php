<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $throttleKey = 'register:'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            return back()->withErrors(['email' => 'Too many registration attempts. Please try again later.']);
        }
        RateLimiter::hit($throttleKey, 600); // 3 attempts / 10 minutes

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:190', 'unique:users,email'],
            'password' => [
                'required', 'string', 'confirmed',
                'min:'.config('security.password_min_length', 10),
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[^A-Za-z0-9]/',
            ],
        ], [
            'password.regex' => 'Password must include an uppercase letter, a lowercase letter, a number, and a symbol.',
        ]);

        $userId = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'user_type' => 'customer',
                'status' => 'pending',
            ]);

            $customerRole = Role::where('slug', 'customer')->first();
            if ($customerRole) {
                $user->roles()->syncWithoutDetaching([$customerRole->id]);
            }

            return $user->id;
        });

        $this->dispatchVerificationEmail($userId, $validated['email']);

        AuditLog::record($userId, 'register', 'auth', $userId, null, ['email' => $validated['email']]);

        return redirect()->route('login')
            ->with('flash_success', 'Account created. Please check your email to verify your address before logging in.');
    }

    private function dispatchVerificationEmail(int $userId, string $email): void
    {
        $token = Str::random(64);

        DB::table('email_verifications')->insert([
            'user_id' => $userId,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addHour(),
            'created_at' => now(),
        ]);

        // Real mail dispatch is a Phase 6+ addition (queued Mailable). For
        // now the verification link is logged so the flow is testable
        // end-to-end without a configured mail transport — matches the
        // same pattern used in the Core PHP build.
        Log::info('Verification email queued', [
            'user_id' => $userId,
            'link' => route('verification.verify', ['token' => $token]),
        ]);
    }
}
