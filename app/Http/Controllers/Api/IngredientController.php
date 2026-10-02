<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Services\IngredientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class IngredientController extends Controller
{
    public function __construct(
        private readonly IngredientService $ingredientService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $ingredients = $this->ingredientService->list(
            query: $request->query('q'),
            categoryId: $request->integer('category_id'),
        );

        return response()->json([
            'success' => true,
            'data' => $ingredients->items(),
            'meta' => [
                'current_page' => $ingredients->currentPage(),
                'last_page' => $ingredients->lastPage(),
                'per_page' => $ingredients->perPage(),
                'total' => $ingredients->total(),
            ],
        ]);
    }

    public function show(Ingredient $ingredient): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->ingredientService->find($ingredient->id),
        ]);
    }

    public function categories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->ingredientService->categories(),
        ]);
    }

    public function image(string $file): BinaryFileResponse
    {
        if (!preg_match('/^[A-Za-z0-9._-]+$/', $file)) {
            abort(404);
        }

        $full = public_path('ingredients/' . $file);

        if (!is_file($full)) {
            abort(404);
        }

        return response()->file($full);
    }
}
