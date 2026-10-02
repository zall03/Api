<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildMeasurement;
use App\Models\UserStock;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly StockService $stockService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $allStocks = $this->stockService->listForUser($user);

        $expiring = collect($allStocks)
            ->filter(fn (UserStock $s) => in_array($s->status, ['expiring', 'expired'], true))
            ->values();

        $lowStock = collect($allStocks)
            ->filter(fn (UserStock $s) => $s->status !== 'expired' && $this->isLowStock((float) $s->quantity, $s->unit))
            ->sortBy('quantity')
            ->take(8)
            ->values();

        $measurementDue = Child::where('user_id', $user->id)
            ->get()
            ->map(function (Child $child) {
                $last = ChildMeasurement::where('child_id', $child->id)
                    ->orderBy('measured_at', 'desc')
                    ->first();
                $days = $last
                    ? (int) now()->startOfDay()->diffInDays($last->measured_at->startOfDay())
                    : null;

                return [
                    'child_id' => $child->id,
                    'name' => $child->name,
                    'gender' => $child->gender,
                    'birth_date' => $child->birth_date?->toDateString(),
                    'last_measured_at' => $last?->measured_at?->toDateString(),
                    'days_since_last' => $days,
                    'last_weight' => $last?->weight,
                    'last_height' => $last?->height,
                ];
            })
            ->filter(fn (array $m) => $m['last_measured_at'] === null || ($m['days_since_last'] ?? 0) >= 30)
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'expiring' => $expiring,
                'low_stock' => $lowStock,
                'measurement_due' => $measurementDue,
            ],
        ]);
    }

    private function isLowStock(float $qty, string $unit): bool
    {
        $u = strtolower(trim($unit));

        return match (true) {
            str_contains($u, 'kg'), str_contains($u, 'kilogram') => $qty < 0.5,
            str_contains($u, 'g') => $qty < 100,
            str_contains($u, 'ml') => $qty < 250,
            str_contains($u, 'liter'), str_ends_with($u, 'l') => $qty < 0.5,
            default => $qty <= 1,
        };
    }
}