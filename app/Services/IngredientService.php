<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class IngredientService
{
    public function list(?string $query, ?int $categoryId): LengthAwarePaginator
    {
        return Ingredient::query()
            ->with('category:id,name')
            ->when($query, fn ($q) => $q->where('name', 'like', "%{$query}%"))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
    }

    public function find(int $id): ?Ingredient
    {
        return Ingredient::with('category:id,name')->find($id);
    }

    public function categories(): Collection
    {
        return IngredientCategory::orderBy('name')->get();
    }
}
