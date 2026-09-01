<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PassType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'duration_months', 'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_months' => 'integer',
        ];
    }

    public function isMonthly(): bool
    {
        return $this->type === 'monthly';
    }

    public function isSingle(): bool
    {
        return $this->type === 'single';
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'pass_type_id');
    }

    public function getDurationLabelAttribute(): ?string
    {
        if ($this->isSingle()) {
            return null;
        }

        return match ($this->duration_months) {
            1 => __('1 month'),
            2 => __('2 months'),
            3 => __('3 months'),
            6 => __('6 months'),
            12 => __('12 months'),
            default => $this->duration_months . ' ' . __('months'),
        };
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->name) {
            return $this->name;
        }

        if ($this->isSingle()) {
            return __('Single Pass');
        }

        return __('Monthly Pass') . ' (' . $this->duration_label . ')';
    }

    public function computeValidity(Carbon $from): array
    {
        if ($this->isSingle()) {
            return [
                'valid_from' => $from->copy()->startOfDay(),
                'valid_until' => $from->copy()->endOfDay(),
            ];
        }

        $start = $from->copy()->startOfMonth();

        return [
            'valid_from' => $start,
            'valid_until' => $start->copy()->addMonths($this->duration_months ?? 1)->subDay()->endOfDay(),
        ];
    }
}
