<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\UserStock;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecipeController extends Controller
{
    public function index(): JsonResponse
    {
        $recipes = Recipe::with(['recipeIngredients.ingredient'])->get()->map(fn ($r) => self::present($r));

        return response()->json(['success' => true, 'data' => $recipes]);
    }

    public function show(int $id): JsonResponse
    {
        $r = Recipe::with(['recipeIngredients.ingredient'])->find($id);
        if (!$r) {
            return response()->json(['success' => false, 'message' => 'Resep tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => self::present($r)]);
    }

    /**
     * Generate resep berbasis bahan stok user via Google Gemini.
     *
     * Body: { "ingredient_ids": [1,2,3] } — id bahan yang dimiliki user di stok.
     */
    public function generate(Request $request): JsonResponse
    {
        $gemini = GeminiService::fromConfig();
        $data = $request->validate([
            'ingredient_ids' => ['required', 'array', 'min:1'],
            'ingredient_ids.*' => 'integer|distinct',
        ]);

        $stockIngredients = UserStock::where('user_id', $request->user()->id)
            ->whereIn('ingredient_id', $data['ingredient_ids'])
            ->get()
            ->map(fn ($s) => $s->ingredient)
            ->filter()
            ->values();

        if ($stockIngredients->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada bahan yang cocok dengan stok kamu. Pilih bahan dari daftar stok.',
            ], 422);
        }

        $names = $stockIngredients->map(fn ($i) => $i->name)->all();

        $prompt = <<<PROMPT
Kamu adalah koki rumah tangga untuk ibu Indonesia yang ingin menyajikan makanan bergizi murah untuk balita. Bahan yang tersedia: {{NAMES}}.

Buatlah SATU resep masakan yang memanfaatkan bahan-bahan tersebut. Boleh menambahkan bahan pelengkap umum seperti bumbu dapur, garam, minyak, bawang, dsb.

Kembalikan HANYA JSON tanpa penjelasan lain, dengan format persis:
{
  "name": "Nama masakan",
  "description": "1-2 kalimat tentang masakan ini",
  "instructions": ["langkah 1", "langkah 2", "..."],
  "cook_time_minutes": 30,
  "ingredients": [
    {"ingredient_name": "nama bahan", "quantity": 1.5, "unit": "gram"},
    {"ingredient_name": "nama bahan", "quantity": 2, "unit": "siung"}
  ]
}

Gunakan quantity berupa angka desimal/angka, unit memakai satuan biasa Indonesia (gram, sdm, sdt, siung, butir, buah, gelas). Pastikan bahan utama yang tersedia dipakai.
PROMPT;
        $prompt = str_replace('{{NAMES}}', implode(', ', $names), $prompt);

        try {
            $result = $gemini->generateJson($prompt);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }

        $name = is_string($result['name'] ?? null) ? trim($result['name']) : 'Resep Tanpa Nama';
        $description = is_string($result['description'] ?? null) ? trim($result['description']) : null;
        $instructions = $this->normalizeInstructions($result['instructions'] ?? null);
        $cookTime = is_numeric($result['cook_time_minutes'] ?? null) ? max(1, (int) $result['cook_time_minutes']) : 30;

        $recipe = DB::transaction(function () use ($name, $description, $instructions, $cookTime, $request, $result) {
            $recipe = Recipe::create([
                'name' => $name,
                'description' => $description,
                'instructions' => $instructions,
                'cook_time_minutes' => $cookTime,
                'source' => 'ai_generated',
                'created_by' => $request->user()->id,
            ]);

            $unit = strtolower(trim((string) ($result['unit'] ?? '')));

            foreach ((array) ($result['ingredients'] ?? []) as $item) {
                if (! is_array($item)) {
                    continue;
                }
                $ingName = trim((string) ($item['ingredient_name'] ?? ''));
                $quantity = is_numeric($item['quantity'] ?? null) ? max(0.01, (float) $item['quantity']) : 1.0;
                $ingUnit = trim((string) ($item['unit'] ?? '')) ?: $unit;

                $ingredient = Ingredient::whereRaw('LOWER(name) = ?', [strtolower($ingName)])->first();
                if (!$ingredient) {
                    continue;
                }

                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                    'quantity_needed' => $quantity,
                    'unit' => $ingUnit,
                ]);
            }

            return $recipe->load(['recipeIngredients.ingredient']);
        });

        return response()->json(['success' => true, 'data' => self::present($recipe)], 201);
    }

    private function normalizeInstructions(mixed $raw): string
    {
        if (is_string($raw)) {
            return trim($raw);
        }
        if (is_array($raw)) {
            $steps = array_values(array_filter(array_map(
                fn ($s) => trim((string) $s),
                $raw
            )));
            return implode("\n", $steps);
        }
        return '';
    }

    private static function present(Recipe $r): array
    {
        return [
            'id' => $r->id,
            'name' => $r->name,
            'description' => $r->description,
            'instructions' => $r->instructions,
            'cook_time_minutes' => $r->cook_time_minutes,
            'source' => $r->source,
            'ingredients' => $r->recipeIngredients->map(function ($ri) {
                return [
                    'ingredient_id' => $ri->ingredient_id,
                    'ingredient_name' => $ri->ingredient?->name ?? 'Unknown',
                    'quantity_needed' => (float) $ri->quantity_needed,
                    'unit' => $ri->unit,
                    'image_url' => $ri->ingredient?->image_url,
                ];
            })->values(),
        ];
    }
}