<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center">
        <i class="bi bi-shield-lock" style="font-size: 3rem; color: var(--danger);"></i>
        <h1 class="h4 fw-bold mt-3">Access Denied</h1>
        <p class="text-muted">{{ $exception->getMessage() ?: 'You do not have permission to view this page.' }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-2">Go Home</a>
    </div>
</body>
</html>
