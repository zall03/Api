<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IngredientCategory extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'category_id');
    }
}
