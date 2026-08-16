<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'module'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions');
    }

    /** Grouped by module — used to render the permission matrix on the role form. */
    public static function groupedByModule(): array
    {
        return static::orderBy('module')->orderBy('name')->get()->groupBy('module')->all();
    }
}
