<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// ---------------------------------------------------------------------
// Guest-only auth routes
// ---------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::get('/verify-email', [EmailVerificationController::class, 'verify'])->name('verification.verify');

Route::get('/two-factor', [TwoFactorChallengeController::class, 'show'])->name('two-factor.show');
Route::post('/two-factor', [TwoFactorChallengeController::class, 'store'])->name('two-factor.store');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ---------------------------------------------------------------------
// Authenticated customer landing (Phase 1 shell — full storefront later)
// ---------------------------------------------------------------------
Route::middleware(['auth', 'account.active'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// ---------------------------------------------------------------------
// Admin panel (auth + admin role + active-account required)
// ---------------------------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'account.active', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles.view');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles.create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles.edit');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles.delete');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:permissions.view');

    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:customers.view');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:customers.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:customers.edit');
    Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend')->middleware('permission:customers.edit');
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate')->middleware('permission:customers.edit');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index')->middleware('permission:audit_logs.view');

    Route::get('/security/2fa', [SecurityController::class, 'show'])->name('security.2fa');
    Route::post('/security/2fa/enable', [SecurityController::class, 'enable'])->name('security.2fa.enable');
    Route::post('/security/2fa/confirm', [SecurityController::class, 'confirm'])->name('security.2fa.confirm');
    Route::post('/security/2fa/disable', [SecurityController::class, 'disable'])->name('security.2fa.disable');
});
