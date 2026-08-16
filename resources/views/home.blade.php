<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0F6E5C">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .top-bar { background: var(--surface); border-bottom: 1px solid var(--border-soft); padding: 0.9rem 0; }
        .brand-mark { font-family: 'Sora', sans-serif; font-weight: 800; font-size: 1.375rem; color: var(--teal-700); }
        .brand-mark .dot { color: var(--marigold-500); }
        .welcome-card { max-width: 560px; margin: 4rem auto; text-align: center; }
    </style>
</head>
<body>
<div class="top-bar">
    <div class="container d-flex justify-content-between align-items-center">
        <span class="brand-mark">{{ config('app.name') }}<span class="dot">.</span></span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-secondary btn-sm">Log Out</button>
        </form>
    </div>
</div>

<div class="container py-4">
    @if (session('flash_success'))
        <div class="alert alert-success">{{ session('flash_success') }}</div>
    @endif

    <div class="welcome-card">
        <h1 class="h3 fw-bold mb-1">Welcome, {{ $user->name }}</h1>
        <p class="text-muted">
            The storefront (browsing, cart, checkout) is a later phase — this
            confirms your account, authentication, and session are all working
            correctly.
        </p>
        @if ($user->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary mt-2">Go to Admin Dashboard</a>
        @endif
    </div>
</div>
</body>
</html>
