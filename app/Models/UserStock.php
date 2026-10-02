<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStock extends Model
{
    protected $fillable = [
        'user_id',
        'ingredient_id',
        'quantity',
        'unit',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'expiry_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
