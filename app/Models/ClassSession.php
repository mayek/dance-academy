<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'dance_group_id', 'date', 'start_time', 'end_time', 'room', 'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function danceGroup(): BelongsTo
    {
        return $this->belongsTo(DanceGroup::class, 'dance_group_id');
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function durationHours(): float
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        $minutes = $end->gt($start) ? $end->diffInMinutes($start, true) : 60;

        return round($minutes / 60, 2);
    }

    public function scopePlanned($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}