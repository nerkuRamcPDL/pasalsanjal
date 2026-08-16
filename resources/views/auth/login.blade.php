<x-layouts.auth>
    <h1 class="h4 fw-bold mb-1">Welcome back</h1>
    <p class="text-muted small mb-4">Sign in to continue to your account.</p>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember">
            <label class="form-check-label small" for="remember">Remember me</label>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Sign In</button>
        </div>
    </form>
    <p class="text-center small text-muted mt-4 mb-0">
        Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none">Create one</a>
    </p>
</x-layouts.auth>
