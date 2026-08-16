<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'avatar', 'user_type', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token', 'totp_secret', 'totp_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'totp_enabled' => 'boolean',
        ];
    }

    // ---- RBAC ----

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles');
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles->pluck('slug')->intersect($slugs)->isNotEmpty();
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->hasRole('super-admin')) {
            return true; // Super admin bypasses granular checks by design
        }

        return $this->roles
            ->loadMissing('permissions')
            ->pluck('permissions')
            ->flatten()
            ->pluck('slug')
            ->contains($slug);
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super-admin', 'administrator']);
    }

    // ---- Relationships ----

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ---- Account lockout (SRS 9.4) ----

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function registerFailedLogin(): void
    {
        $attempts = $this->failed_login_attempts + 1;
        $threshold = (int) config('security.lockout_threshold', 5);

        $this->failed_login_attempts = $attempts;

        if ($attempts >= $threshold) {
            $this->locked_until = now()->addMinutes((int) config('security.lockout_minutes', 15));
        }

        $this->save();
    }

    public function clearFailedLogins(): void
    {
        $this->forceFill(['failed_login_attempts' => 0, 'locked_until' => null])->save();
    }

    public function isActive(): bool
    {
        return ! in_array($this->status, ['suspended', 'blocked'], true);
    }
}
