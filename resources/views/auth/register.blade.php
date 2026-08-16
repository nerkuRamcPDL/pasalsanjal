<x-layouts.auth>
    <h1 class="h4 fw-bold mb-1">Create your account</h1>
    <p class="text-muted small mb-4">Join {{ config('app.name') }} to start shopping.</p>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            <div class="form-text">At least 10 characters, with uppercase, lowercase, a number, and a symbol.</div>
            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Create Account</button>
        </div>
    </form>
    <p class="text-center small text-muted mt-4 mb-0">
        Already have an account? <a href="{{ route('login') }}" class="text-decoration-none">Sign in</a>
    </p>
</x-layouts.auth>
