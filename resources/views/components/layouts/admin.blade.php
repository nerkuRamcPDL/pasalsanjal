<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0F6E5C">
    <title>{{ config('app.name') }} — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --sidebar-w: 250px; }
        .sidebar { width: var(--sidebar-w); min-height: 100vh; background: #1E293B; color: #E2E8F0; position: fixed; top: 0; left: 0; z-index: 1030; }
        .sidebar .brand { padding: 1.25rem; font-weight: 700; font-size: 1.1rem; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.08); font-family: 'Sora', sans-serif; }
        .sidebar .nav-link { color: #CBD5E1; padding: 0.6rem 1.25rem; font-size: 0.9rem; border-radius: 0; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(37,99,235,0.15); color: #fff; border-left: 3px solid var(--teal-500); padding-left: calc(1.25rem - 3px); }
        .sidebar .nav-section { padding: 0.75rem 1.25rem 0.25rem; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748B; }
        .main-content { margin-left: var(--sidebar-w); }
        .topbar { background: var(--surface); border-bottom: 1px solid var(--border-soft); padding: 0.75rem 1.5rem; }
        @media (max-width: 767.98px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.2s ease; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="sidebar d-flex flex-column" id="adminSidebar">
    <div class="brand">{{ config('app.name') }} <span class="badge bg-primary ms-1">Admin</span></div>
    <nav class="nav flex-column py-2 flex-grow-1">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i>Dashboard
        </a>

        <div class="nav-section">Access Control</div>
        <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
            <i class="bi bi-shield-check"></i>Roles
        </a>
        <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
            <i class="bi bi-key"></i>Permissions
        </a>
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
            <i class="bi bi-people"></i>Users
        </a>

        <div class="nav-section">Security</div>
        <a class="nav-link {{ request()->routeIs('admin.security.*') ? 'active' : '' }}" href="{{ route('admin.security.2fa') }}">
            <i class="bi bi-shield-lock"></i>Two-Factor Auth
        </a>
        <a class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}">
            <i class="bi bi-journal-text"></i>Audit Logs
        </a>

        <div class="nav-section">Coming in Phase 2+</div>
        <span class="nav-link text-muted" style="cursor: not-allowed;"><i class="bi bi-shop"></i>Vendors</span>
        <span class="nav-link text-muted" style="cursor: not-allowed;"><i class="bi bi-box-seam"></i>Products</span>
    </nav>
    <form method="POST" action="{{ route('logout') }}" class="p-3 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
        @csrf
        <button class="btn btn-sm btn-outline-light w-100">Log Out</button>
    </form>
</div>

<div class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center">
        <button class="btn btn-sm btn-outline-secondary d-md-none" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <div class="d-none d-md-block"></div>
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted">{{ auth()->user()->name }}</span>
            <span class="badge bg-secondary text-capitalize">{{ auth()->user()->user_type }}</span>
        </div>
    </div>

    <div class="p-4">
        @if (session('flash_success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('flash_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('flash_error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('flash_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{ $slot }}
    </div>
</div>
</body>
</html>
