<x-layouts.admin>
    <h1 class="h4 fw-bold mb-4">Set Up Two-Factor Authentication</h1>

    <div class="card" style="max-width: 480px;">
        <div class="card-body">
            <ol class="small text-muted ps-3">
                <li class="mb-2">Install an authenticator app (Google Authenticator, Authy, etc.)</li>
                <li class="mb-2">Scan the QR code below</li>
                <li>Enter the 6-digit code it shows to confirm</li>
            </ol>

            <div class="text-center my-4 p-3" style="background: var(--bg); border-radius: var(--radius);">
                {!! $qrSvg !!}
            </div>

            <p class="small text-muted text-center mb-3">
                Can't scan? Enter this code manually:<br>
                <code>{{ $secret }}</code>
            </p>

            <form method="POST" action="{{ route('admin.security.2fa.confirm') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Verification Code</label>
                    <input type="text" name="code" class="form-control text-center" style="letter-spacing: 0.5rem;" maxlength="6" pattern="\d{6}" inputmode="numeric" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary w-100">Confirm &amp; Enable</button>
            </form>
        </div>
    </div>
</x-layouts.admin>
