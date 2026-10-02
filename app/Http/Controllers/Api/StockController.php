<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserStock;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $stocks = $this->stockService->listForUser(
            user: $request->user(),
            categoryId: $request->integer('category_id'),
            status: $request->query('status'),
            expiringWithinDays: $request->integer('within_days', 3),
        );

        return response()->json(['success' => true, 'data' => $stocks]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ingredient_id' => 'required|exists:ingredients_master,id',
            'quantity' => 'required|numeric|gt:0',
            'unit' => 'required|string|max:20',
            'expiry_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $stock = $this->stockService->create($request->user(), $validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil ditambahkan',
            'data' => $stock,
        ], 201);
    }

    public function show(Request $request, UserStock $stock): JsonResponse
    {
        $stock = $this->stockService->findForUser($request->user(), $stock->id);

        if (! $stock) {
            return response()->json(['success' => false, 'message' => 'Stok tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $stock]);
    }

    public function update(Request $request, UserStock $stock): JsonResponse
    {
        $stock = $this->stockService->findForUser($request->user(), $stock->id);

        if (! $stock) {
            return response()->json(['success' => false, 'message' => 'Stok tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|numeric|gt:0',
            'unit' => 'sometimes|string|max:20',
            'expiry_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $updated = $this->stockService->update($request->user(), $stock->id, $validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil diperbarui',
            'data' => $updated,
        ]);
    }

    public function destroy(Request $request, UserStock $stock): JsonResponse
    {
        if (! $this->stockService->delete($request->user(), $stock->id)) {
            return response()->json(['success' => false, 'message' => 'Stok tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Stok berhasil dihapus']);
    }
}
