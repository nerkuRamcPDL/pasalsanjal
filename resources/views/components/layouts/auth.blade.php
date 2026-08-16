<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0F6E5C">
    <title>{{ config('app.name') }} — Sign In</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(160deg, var(--teal-700) 0%, var(--teal-600) 45%, var(--teal-500) 100%);
        }
        .auth-wrap { min-height: 100vh; display: flex; align-items: center; padding: 2rem 1rem; }
        .brand-mark { font-family: 'Sora', sans-serif; font-weight: 800; font-size: 1.5rem; color: #fff; }
        .brand-mark .dot { color: var(--marigold-400); }
        .auth-card { border: none; border-radius: var(--radius-lg); box-shadow: 0 24px 60px rgba(10, 63, 53, 0.35); }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="text-center mb-4">
                    <div class="brand-mark mb-1">{{ config('app.name') }}<span class="dot">.</span></div>
                    <p class="mb-0" style="color: rgba(255,255,255,0.8);">Multi-Vendor Marketplace</p>
                </div>

                @if (session('flash_success'))
                    <div class="alert alert-success">{{ session('flash_success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <div class="card auth-card">
                    <div class="card-body p-4 p-md-5">
                        {{ $slot }}
                    </div>
                </div>
                <p class="text-center small mt-4 mb-0" style="color: rgba(255,255,255,0.7);">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
