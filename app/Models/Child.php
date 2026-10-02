<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;

class Child extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'birth_date',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAgeInMonthsAttribute(): int
    {
        return Date::now()->diffInMonths($this->birth_date);
    }

    public function getAgeInYearsAttribute(): int
    {
        return intdiv($this->age_in_months, 12);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date->toDateString(),
            'age_in_months' => $this->age_in_months,
            'age_in_years' => $this->age_in_years,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}