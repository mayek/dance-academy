<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DanceGroup;
use App\Models\Payment;
use App\Models\User;
use App\Services\AttendanceAccounting;
use App\Services\MonthlyPassService;
use App\Services\RosterService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherAttendanceController extends Controller
{
    public function create(Request $request)
    {
        $user = auth()->user();
        $groups = $user->taughtGroups()->with(['category', 'students'])->orderBy('name')->get();
        $selectedGroup = $request->get('group_id');
        $selectedDate = $request->get('date', now()->format('Y-m-d'));

        $roster = collect();
        $existingRecords = collect();
        $activePassByStudent = collect();
        $makeupAbsences = collect();

        if ($selectedGroup) {
            $group = $groups->firstWhere('id', $selectedGroup);
            if ($group) {
                $passDate = Carbon::parse($selectedDate);

                $roster = app(RosterService::class)->for($group, $passDate);

                $existingRecords = Attendance::where('dance_group_id', $selectedGroup)
                    ->whereDate('date', $selectedDate)
                    ->pluck('status', 'student_id');

                $studentIds = $roster->pluck('id');
                if ($studentIds->isNotEmpty()) {
                    $monthlyPasses = Payment::query()
                        ->where('pass_type', 'monthly')
                        ->whereNull('dance_group_id')
                        ->whereIn('student_id', $studentIds)
                        ->where('status', 'active')
                        ->whereDate('valid_from', '<=', $passDate->toDateString())
                        ->whereDate('valid_until', '>=', $passDate->toDateString())
                        ->orderBy('valid_from')
                        ->orderBy('id')
                        ->get()
                        ->keyBy('student_id');

                    $singlePasses = Payment::query()
                        ->whereIn('student_id', $studentIds)
                        ->where('status', 'active')
                        ->whereDate('valid_from', '<=', $passDate->toDateString())
                        ->whereDate('valid_until', '>=', $passDate->toDateString())
                        ->whereNotNull('total_hours')
                        ->orderBy('valid_from')
                        ->orderBy('id')
                        ->get()
                        ->keyBy('student_id');

                    $activePassByStudent = $studentIds
                        ->mapWithKeys(fn ($id) => [$id => $monthlyPasses->get($id) ?? $singlePasses->get($id)])
                        ->filter();
                }

                $makeupAbsences = app(RosterService::class)
                    ->makeupAbsences(Carbon::parse($selectedDate))
                    ->whereNotIn('student_id', $roster->pluck('id'));
            }
        }

        return view('teacher.attendance.create', compact(
            'groups',
            'selectedGroup',
            'selectedDate',
            'roster',
            'existingRecords',
            'activePassByStudent',
            'makeupAbsences'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'date' => ['required', 'date'],
            'statuses' => ['required', 'array'],
            'statuses.*' => ['required', 'in:present,absent,excused'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string'],
        ]);

        $group = DanceGroup::findOrFail($validated['dance_group_id']);
        abort_unless($group->teacher_id === $user->id, 403);

        $roster = app(RosterService::class)->for($group, Carbon::parse($validated['date']));
        $rosterTypes = $roster->pluck('roster_type', 'id');

        $accounting = app(AttendanceAccounting::class);
        $warnings = [];

        foreach ($validated['statuses'] as $studentId => $status) {
            if (!$rosterTypes->has((int) $studentId)) {
                continue;
            }

            $type = $rosterTypes[(int) $studentId];

            $attendance = Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'dance_group_id' => $validated['dance_group_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $status,
                    'notes' => $validated['notes'][$studentId] ?? null,
                    'recorded_by' => $user->id,
                ]
            );

            if ($type === 'one_time' || $attendance->made_up_for_attendance_id !== null) {
                if ($attendance->made_up_for_attendance_id === null) {
                    $hasSinglePass = Payment::where('student_id', $studentId)
                        ->where('status', 'active')
                        ->where('valid_until', '>=', now()->startOfDay())
                        ->whereNotNull('total_hours')
                        ->whereColumn('used_hours', '<', 'total_hours')
                        ->exists();

                    if ($hasSinglePass) {
                        $warnings = array_merge($warnings, $accounting->apply($attendance));
                    } else {
                        $attendance->update([
                            'payment_id' => null,
                            'hours_consumed' => 0,
                        ]);
                    }
                }

                continue;
            }

            $warnings = array_merge($warnings, $accounting->apply($attendance));
        }

        $result = redirect()->route('teacher.attendance.create', [
            'group_id' => $validated['dance_group_id'],
            'date' => $validated['date'],
        ])->with('success', __('Attendance recorded successfully'));

        if (!empty($warnings)) {
            $result->with('warning', array_unique($warnings));
        }

        return $result;
    }

    public function addSingle(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'date' => ['required', 'date'],
            'student_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:one_time,makeup'],
            'absence_id' => ['nullable', 'exists:attendances,id'],
        ]);

        $group = DanceGroup::findOrFail($validated['dance_group_id']);
        abort_unless($group->teacher_id === $user->id, 403);

        $date = Carbon::parse($validated['date']);
        $student = User::findOrFail($validated['student_id']);
        $roster = app(RosterService::class);
        $monthly = app(MonthlyPassService::class);

        $back = fn ($bag, $message, bool $success = true) => redirect()->route('teacher.attendance.create', [
            'group_id' => $group->id,
            'date' => $date->toDateString(),
        ])->with($success ? 'success' : 'warning', $message);

        if ($validated['type'] === 'one_time') {
            $errors = $roster->addOneTime($group, $date, $student, $request->get('notes'));

            return empty($errors)
                ? $back('success', __('Dodano wejście jednorazowe dla :name', ['name' => $student->full_name]))
                : $back('warning', $errors, false);
        }

        $absence = Attendance::where('id', $validated['absence_id'])
            ->where('student_id', $student->id)
            ->where('status', 'absent')
            ->where('made_up', false)
            ->first();

        if ($absence === null) {
            return $back('warning', __('Wybierz nieodrobioną nieobecność ucznia.'), false);
        }

        $warnings = $monthly->markMadeUp($absence, $group->id, $date);

        return empty($warnings)
            ? $back('success', __('Nieobecność odrobiona — odnotowano jako odrabianie.'))
            : $back('warning', $warnings, false);
    }

    public function history(DanceGroup $group)
    {
        $user = auth()->user();
        abort_unless($group->teacher_id === $user->id, 403);

        $attendances = Attendance::with(['student', 'recordedBy', 'payment'])
            ->where('dance_group_id', $group->id)
            ->orderBy('date', 'desc')
            ->orderBy('student_id')
            ->paginate(20);

        return view('teacher.attendance.history', compact('group', 'attendances'));
    }
}
