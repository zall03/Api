<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingredient extends Model
{
    protected $table = 'ingredients_master';

    protected $fillable = [
        'category_id',
        'name',
        'default_unit',
        'image_path',
        'calories_per_100g',
        'protein_g',
        'fat_g',
        'carbs_g',
        'iron_mg',
        'zinc_mg',
        'vitamin_a_mcg',
        'vitamin_c_mg',
    ];

    protected $appends = ['image_url'];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'calories_per_100g' => 'float',
            'protein_g' => 'float',
            'fat_g' => 'float',
            'carbs_g' => 'float',
            'iron_mg' => 'float',
            'zinc_mg' => 'float',
            'vitamin_a_mcg' => 'float',
            'vitamin_c_mg' => 'float',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IngredientCategory::class, 'category_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_path;
        if (empty($path)) {
            return null;
        }

        $host = request()?->getSchemeAndHttpHost() ?? (string) config('app.url');

        return rtrim($host, '/') . '/api/ingredients/image/' . rawurlencode(basename($path));
    }
}
