<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmailVerificationController extends Controller
{
    public function verify(Request $request): RedirectResponse
    {
        $token = (string) $request->query('token', '');
        if ($token === '') {
            return redirect()->route('login')->withErrors(['email' => 'Invalid verification link.']);
        }

        $hash = hash('sha256', $token);
        $record = DB::table('email_verifications')
            ->where('token_hash', $hash)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            return redirect()->route('login')->withErrors(['email' => 'This verification link is invalid or has expired.']);
        }

        DB::table('email_verifications')->where('id', $record->id)->update(['verified_at' => now()]);
        DB::table('users')->where('id', $record->user_id)->update([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        AuditLog::record($record->user_id, 'email_verified', 'auth', $record->user_id);

        return redirect()->route('login')->with('flash_success', 'Email verified. You can now log in.');
    }
}
