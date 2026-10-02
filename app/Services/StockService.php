<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StockService
{
    public function listForUser(
        User $user,
        ?int $categoryId = null,
        ?string $status = null,
        ?int $expiringWithinDays = null,
    ): Collection {
        return UserStock::query()
            ->with('ingredient.category:id,name')
            ->where('user_id', $user->id)
            ->when($categoryId, fn (Builder $q) => $q->whereHas('ingredient', fn (Builder $i) => $i->where('category_id', $categoryId)))
            ->when($status === 'expiring', function (Builder $q) use ($expiringWithinDays) {
                $cutoff = now()->addDays($expiringWithinDays ?? 3);
                $q->whereNotNull('expiry_date')
                    ->where('expiry_date', '<=', $cutoff->toDateString())
                    ->where('expiry_date', '>=', now()->toDateString());
            })
            ->when($status === 'expired', function (Builder $q) {
                $q->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', now()->toDateString());
            })
            ->get()
            ->map(fn (UserStock $stock) => $this->present($stock));
    }

    public function findForUser(User $user, int $id): ?UserStock
    {
        $stock = UserStock::query()
            ->with('ingredient.category:id,name')
            ->where('user_id', $user->id)
            ->find($id);

        return $stock ? $this->present($stock) : null;
    }

    public function create(User $user, array $data): UserStock
    {
        $existing = UserStock::where('user_id', $user->id)
            ->where('ingredient_id', $data['ingredient_id'])
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + (float) $data['quantity'],
                'unit' => $data['unit'] ?? $existing->unit,
                'expiry_date' => $data['expiry_date'] ?? $existing->expiry_date,
            ]);

            return $this->findForUser($user, $existing->id);
        }

        $stock = UserStock::create([
            'user_id' => $user->id,
            'ingredient_id' => $data['ingredient_id'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'expiry_date' => $data['expiry_date'] ?? null,
        ]);

        return $this->findForUser($user, $stock->id);
    }

    public function update(User $user, int $id, array $data): ?UserStock
    {
        $stock = $this->findForUser($user, $id);

        if (! $stock) {
            return null;
        }

        $stock->update($data);

        return $this->findForUser($user, $id);
    }

    public function delete(User $user, int $id): bool
    {
        $stock = $this->findForUser($user, $id);

        if (! $stock) {
            return false;
        }

        return (bool) $stock->delete();
    }

    private function present(UserStock $stock): UserStock
    {
        if (! $stock->expiry_date) {
            $stock->setAttribute('status', 'fresh');
            $stock->setAttribute('days_left', null);

            return $stock;
        }

        $today = now()->startOfDay();
        $expiry = $stock->expiry_date->startOfDay();
        $daysLeft = (int) $today->diffInDays($expiry, false);

        $stock->setAttribute('days_left', $daysLeft);
        $stock->setAttribute('status', $this->statusFor($daysLeft));

        return $stock;
    }

    private function statusFor(int $daysLeft): string
    {
        return match (true) {
            $daysLeft < 0 => 'expired',
            $daysLeft <= 3 => 'expiring',
            default => 'fresh',
        };
    }
}
