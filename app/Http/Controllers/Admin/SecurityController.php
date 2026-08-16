<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function __construct(private readonly TotpService $totp) {}

    public function show(): View
    {
        return view('admin.security.two-factor', ['user' => Auth::user()]);
    }

    /** Step 1: generate a secret + QR provisioning URI, hold in session until confirmed. */
    public function enable(): View
    {
        $secret = $this->totp->generateSecret();
        session(['pending_totp_secret' => $secret]);

        $user = Auth::user();
        $uri = $this->totp->provisioningUri($secret, $user->email, config('app.name'));

        return view('admin.security.two-factor-setup', [
            'secret' => $secret,
            'qrSvg' => $this->renderQrSvg($uri),
        ]);
    }

    /**
     * Server-side SVG QR rendering via bacon/bacon-qr-code — no external
     * API call (unlike a common shortcut of hitting a third-party QR
     * image service, which would leak the provisioning URI, and with it
     * the TOTP secret, to that third party over the network).
     *
     * Honesty note: this exact bacon/bacon-qr-code v3 API (ImageRenderer +
     * SvgImageBackEnd + RendererStyle + Writer) is the long-stable,
     * documented usage pattern, but I can't run `composer install` in my
     * sandbox to execute this against the real package — verify this
     * renders correctly as your first test after installing.
     */
    private function renderQrSvg(string $uri): string
    {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(220),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);

        return $writer->writeString($uri);
    }

    /** Step 2: verify a code against the pending secret before turning 2FA on. */
    public function confirm(Request $request): View|RedirectResponse
    {
        $validated = $request->validate(['code' => ['required', 'string']]);

        $secret = session('pending_totp_secret');
        if (! $secret || ! $this->totp->verify($secret, $validated['code'])) {
            return back()->withErrors(['code' => 'Invalid code. Please scan the QR code again and try the current 6-digit code.']);
        }

        $recoveryCodes = $this->totp->generateRecoveryCodes();
        $user = Auth::user();

        $user->forceFill([
            'totp_secret' => $secret,
            'totp_enabled' => true,
            'totp_recovery_codes' => Hash::make(implode(',', $recoveryCodes)),
        ])->save();

        session()->forget('pending_totp_secret');
        AuditLog::record($user->id, 'enable_2fa', 'security', $user->id);

        return view('admin.security.recovery-codes', ['codes' => $recoveryCodes]);
    }

    public function disable(): RedirectResponse
    {
        $user = Auth::user();

        $user->forceFill([
            'totp_secret' => null,
            'totp_enabled' => false,
            'totp_recovery_codes' => null,
        ])->save();

        AuditLog::record($user->id, 'disable_2fa', 'security', $user->id);

        return redirect()->route('admin.security.2fa')->with('flash_success', 'Two-factor authentication disabled.');
    }
}
