<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Province extends Model
{
    public $timestamps = false;

    protected $fillable = ['country_id', 'name', 'is_active'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
