<x-layouts.admin>
    <h1 class="h4 fw-bold mb-4">Two-Factor Authentication</h1>

    <div class="card" style="max-width: 560px;">
        <div class="card-body">
            @if ($user->totp_enabled)
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-shield-check" style="font-size: 1.5rem; color: var(--success);"></i>
                    <div>
                        <div class="fw-semibold">Two-factor authentication is enabled</div>
                        <div class="text-muted small">Your account is protected with an authenticator app.</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.security.2fa.disable') }}" onsubmit="return confirm('Disable two-factor authentication? This will make your account less secure.');">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">Disable 2FA</button>
                </form>
            @else
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-shield-exclamation" style="font-size: 1.5rem; color: var(--marigold-600);"></i>
                    <div>
                        <div class="fw-semibold">Two-factor authentication is not enabled</div>
                        <div class="text-muted small">Add an extra layer of security to your account.</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.security.2fa.enable') }}">
                    @csrf
                    <button class="btn btn-primary btn-sm">Enable 2FA</button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.admin>
