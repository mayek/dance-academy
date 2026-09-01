<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DanceGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'teacher_id', 'room', 'schedule', 'class_times', 'start_date', 'end_date'];

    protected function casts(): array
    {
        return [
            'class_times' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DanceCategory::class, 'category_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'dance_group_student', 'dance_group_id', 'student_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActiveForWeek($query, $weekStart, $weekEnd)
    {
        return $query
            ->where(function ($q) use ($weekEnd) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $weekEnd);
            })
            ->where(function ($q) use ($weekStart) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $weekStart);
            });
    }
}
