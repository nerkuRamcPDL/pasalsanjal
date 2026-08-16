<x-layouts.auth>
    <h1 class="h4 fw-bold mb-1">Two-Factor Verification</h1>
    <p class="text-muted small mb-4">Enter the 6-digit code from your authenticator app.</p>

    <form method="POST" action="{{ route('two-factor.store') }}" novalidate>
        @csrf
        <div class="mb-3">
            <input type="text" name="code" class="form-control form-control-lg text-center" style="letter-spacing: 0.5rem;" maxlength="6" pattern="\d{6}" inputmode="numeric" required autofocus>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Verify</button>
        </div>
    </form>
</x-layouts.auth>
