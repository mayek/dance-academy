<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\DanceGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RosterService
{
    public function __construct(
        private ScheduleService $schedule,
        private MonthlyPassService $monthly,
    ) {
    }

    public function for(DanceGroup $group, Carbon $date): Collection
    {
        $dateStr = $date->toDateString();

        $students = collect();

        foreach ($group->students as $student) {
            $joined = $student->pivot->joined_at ?? $student->pivot->created_at;
            $left = $student->pivot->left_at;

            if ($joined && Carbon::parse($joined)->gt($date)) {
                continue;
            }

            if ($left && Carbon::parse($left)->lte($date)) {
                continue;
            }

            $student->setAttribute('roster_type', 'regular');
            $students->push($student);
        }

        $session = $this->schedule->sessionRow($group, $date);

        if ($session !== null && !$session->isCancelled()) {
            foreach ($session->attendees()->where('class_session_user.type', 'one_time')->get() as $attendee) {
                $attendee->setAttribute('roster_type', 'one_time');
                $students->push($attendee);
            }

            $MakeUpIds = Attendance::where('dance_group_id', $group->id)
                ->whereDate('date', $dateStr)
                ->whereNotNull('made_up_for_attendance_id')
                ->pluck('student_id');

            if ($MakeUpIds->isNotEmpty()) {
                foreach (User::whereIn('id', $MakeUpIds)->get() as $student) {
                    $student->setAttribute('roster_type', 'makeup');
                    $students->push($student);
                }
            }
        }

        return $students->unique('id')->values();
    }

    public function rosterIds(DanceGroup $group, Carbon $date): array
    {
        return $this->for($group, $date)->pluck('id')->all();
    }

    public function isOneTime(DanceGroup $group, Carbon $date, int $studentId): bool
    {
        $session = $this->schedule->sessionRow($group, $date);

        if ($session === null || $session->isCancelled()) {
            return false;
        }

        return $session->attendees()
            ->wherePivot('type', 'one_time')
            ->whereKey($studentId)
            ->exists();
    }

    public function addOneTime(DanceGroup $group, Carbon $date, User $student, ?string $notes = null): array
    {
        $session = $this->schedule->sessionRow($group, $date);

        if ($session === null) {
            $hours = $this->schedule->hoursForGroupOnDate($group, $date) ?? 1.0;
            $session = ClassSession::firstOrCreate(
                ['dance_group_id' => $group->id, 'date' => $date->toDateString()],
                [
                    'start_time' => '12:00',
                    'end_time' => gmdate('H:i', max(3600, (int) round($hours * 3600))),
                    'room' => $group->room,
                    'status' => 'planned',
                ]
            );
        }

        if ($session->isCancelled()) {
            return [__('Zajęcia w tym dniu są odwołane.')];
        }

        DB::table('class_session_user')->updateOrInsert(
            ['class_session_id' => $session->id, 'student_id' => $student->id],
            ['type' => 'one_time', 'notes' => $notes, 'created_at' => now(), 'updated_at' => now()]
        );

        return [];
    }

    public function makeupAbsences(Carbon $date): Collection
    {
        return Attendance::with(['student', 'danceGroup.category'])
            ->where('status', 'absent')
            ->where('made_up', false)
            ->whereDate('date', '>=', $date->copy()->subDays($this->monthly::MAKEUP_WINDOW_DAYS)->toDateString())
            ->whereDate('date', '<=', $date->toDateString())
            ->orderBy('date')
            ->orderBy('id')
            ->get();
    }
}