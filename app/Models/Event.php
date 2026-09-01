<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'date', 'start_time', 'end_time', 'room', 'created_by', 'teacher_id'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'start_time' => 'string',
            'end_time' => 'string',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_student', 'event_id', 'student_id');
    }

    public function passStatusByStudent(): array
    {
        $studentIds = $this->students->pluck('id');
        if ($studentIds->isEmpty()) {
            return [];
        }

        return Payment::where('event_id', $this->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->mapWithKeys(fn (Payment $p) => [
                $p->student_id => [
                    'has_pass' => true,
                    'is_paid' => (bool) $p->is_paid,
                ],
            ])
            ->toArray();
    }
}
