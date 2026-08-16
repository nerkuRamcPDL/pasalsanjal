<x-layouts.auth>
    <h1 class="h4 fw-bold mb-1">Forgot your password?</h1>
    <p class="text-muted small mb-4">Enter your email and we'll send you a link to reset it.</p>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </div>
    </form>
    <p class="text-center small text-muted mt-4 mb-0">
        <a href="{{ route('login') }}" class="text-decoration-none">Back to sign in</a>
    </p>
</x-layouts.auth>
