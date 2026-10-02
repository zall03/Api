<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildMeasurement extends Model
{
    protected $fillable = [
        'child_id',
        'weight',
        'height',
        'measured_at',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'float',
            'height' => 'float',
            'measured_at' => 'date',
        ];
    }

    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
}