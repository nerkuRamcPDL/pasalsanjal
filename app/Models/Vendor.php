<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Minimal stub for Phase 1 (User::vendor() needs a resolvable target).
 * Full vendor onboarding/store-management logic lands in Phase 2.
 */
class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'store_name', 'store_slug', 'status', 'commission_rate',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
