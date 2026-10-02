<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'instructions',
        'cook_time_minutes',
        'source',
        'created_by',
    ];

    protected $casts = [
        'cook_time_minutes' => 'integer',
    ];

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }
}