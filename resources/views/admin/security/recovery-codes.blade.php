<x-layouts.admin>
    <h1 class="h4 fw-bold mb-4">Two-Factor Authentication Enabled</h1>

    <div class="card" style="max-width: 480px;">
        <div class="card-body">
            <div class="alert alert-danger small">
                <strong>Save these recovery codes now.</strong> Each can be used once to
                sign in if you lose access to your authenticator app. They will not be
                shown again.
            </div>

            <div class="p-3 mb-3" style="background: var(--bg); border-radius: var(--radius); font-family: monospace;">
                @foreach ($codes as $code)
                    <div class="py-1">{{ $code }}</div>
                @endforeach
            </div>

            <a href="{{ route('admin.security.2fa') }}" class="btn btn-primary w-100">I've Saved My Codes</a>
        </div>
    </div>
</x-layouts.admin>
