<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'first_name', 'last_name', 'email', 'password', 'role',
    'date_of_birth', 'phone_number', 'parent_phone_number',
    'tournament_group', 'notes',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }

        return $this->name;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['teacher', 'reception']);
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function taughtGroups(): HasMany
    {
        return $this->hasMany(DanceGroup::class, 'teacher_id');
    }

    public function enrolledGroups(): BelongsToMany
    {
        return $this->belongsToMany(DanceGroup::class, 'dance_group_student', 'student_id', 'dance_group_id');
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_student', 'student_id', 'event_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    public function activePayments(): HasMany
    {
        return $this->payments()->where('status', 'active')->where('valid_until', '>=', now()->startOfDay());
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function absences(): HasMany
    {
        return $this->attendances()->where('status', 'absent');
    }
}
