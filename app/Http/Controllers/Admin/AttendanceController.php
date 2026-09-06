<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DanceGroup;
use App\Models\Payment;
use App\Models\User;
use App\Services\AttendanceAccounting;
use App\Services\MonthlyPassService;
use App\Services\RosterService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'danceGroup.category', 'recordedBy', 'payment']);

        if ($request->filled('dance_group_id')) {
            $query->where('dance_group_id', $request->dance_group_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->latest('id')->paginate(20);
        $groups = DanceGroup::with('category')->orderBy('name')->get();

        return view('admin.attendance.index', compact('attendances', 'groups'));
    }

    public function create(Request $request)
    {
        $groups = DanceGroup::with(['category', 'students'])->orderBy('name')->get();
        $selectedGroup = $request->get('group_id');
        $selectedDate = $request->get('date', now()->format('Y-m-d'));

        $roster = collect();
        $existingRecords = collect();
        $activePassByStudent = collect();
        $makeupAbsences = collect();

        if ($selectedGroup) {
            $group = DanceGroup::findOrFail($selectedGroup);
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
                    ->where('dance_group_id', $selectedGroup)
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

        return view('admin.attendance.create', compact(
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
        $validated = $request->validate([
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'date' => ['required', 'date'],
            'statuses' => ['required', 'array'],
            'statuses.*' => ['required', 'in:present,absent,excused'],
            'notes' => ['nullable', 'array'],
            'notes.*' => ['nullable', 'string'],
        ]);

        $group = DanceGroup::findOrFail($validated['dance_group_id']);
        $passDate = Carbon::parse($validated['date']);

        $roster = app(RosterService::class)->for($group, $passDate);
        $rosterTypes = $roster->pluck('roster_type', 'id');

        $recordedBy = auth()->id();
        $accounting = app(AttendanceAccounting::class);
        $monthly = app(MonthlyPassService::class);
        $singlePassGroups = $group->id;
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
                    'recorded_by' => $recordedBy,
                ]
            );

            $isOneTime = $type === 'one_time' || $attendance->made_up_for_attendance_id !== null;

            if ($isOneTime) {
                if ($attendance->made_up_for_attendance_id !== null) {
                    continue;
                }

                $hasSinglePass = Payment::where('student_id', $studentId)
                    ->where('dance_group_id', $singlePassGroups)
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

                continue;
            }

            $warnings = array_merge($warnings, $accounting->apply($attendance));
        }

        $result = redirect()->route('admin.attendance.index', [
            'dance_group_id' => $validated['dance_group_id'],
            'date' => $validated['date'],
        ])->with('success', __('Attendance recorded successfully'));

        if (!empty($warnings)) {
            $result->with('warning', array_unique($warnings));
        }

        return $result;
    }

    public function addSingle(Request $request)
    {
        $validated = $request->validate([
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'date' => ['required', 'date'],
            'student_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:one_time,makeup'],
            'absence_id' => ['nullable', 'exists:attendances,id'],
        ]);

        $group = DanceGroup::findOrFail($validated['dance_group_id']);
        $date = Carbon::parse($validated['date']);
        $student = User::findOrFail($validated['student_id']);
        $roster = app(RosterService::class);
        $monthly = app(MonthlyPassService::class);

        if ($validated['type'] === 'one_time') {
            $errors = $roster->addOneTime($group, $date, $student, $request->get('notes'));

            return redirect()->route('admin.attendance.create', [
                'group_id' => $group->id,
                'date' => $date->toDateString(),
            ])->with(empty($errors) ? 'success' : 'warning', empty($errors)
                ? __('Dodano wejście jednorazowe dla :name', ['name' => $student->full_name])
                : $errors);
        }

        $absence = Attendance::where('id', $validated['absence_id'])
            ->where('student_id', $student->id)
            ->where('status', 'absent')
            ->where('made_up', false)
            ->first();

        if ($absence === null) {
            return redirect()->route('admin.attendance.create', [
                'group_id' => $group->id,
                'date' => $date->toDateString(),
            ])->with('warning', __('Wybierz nieodrobioną nieobecność ucznia.'));
        }

        $warnings = $monthly->markMadeUp($absence, $group->id, $date);

        return redirect()->route('admin.attendance.create', [
            'group_id' => $group->id,
            'date' => $date->toDateString(),
        ])->with(empty($warnings) ? 'success' : 'warning', empty($warnings)
            ? __('Nieobecność odrobiona — odnotowano jako odrabianie.')
            : $warnings);
    }

    public function studentAbsences(User $student)
    {
        $absences = Attendance::with(['danceGroup.category', 'recordedBy'])
            ->where('student_id', $student->id)
            ->where('status', 'absent')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $totalAbsences = Attendance::where('student_id', $student->id)
            ->where('status', 'absent')
            ->count();

        $groups = DanceGroup::with('category')
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString()))
            ->orderBy('name')
            ->get();

        return view('admin.attendance.student-absences', compact('student', 'absences', 'totalAbsences', 'groups'));
    }

    public function makeup(Request $request)
    {
        $absences = Attendance::with(['student', 'danceGroup.category'])
            ->where('status', 'absent')
            ->where('made_up', false)
            ->whereDate('date', '<=', now()->toDateString())
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $groups = DanceGroup::with('category')
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString()))
            ->orderBy('name')
            ->get();

        return view('admin.attendance.makeup', compact('absences', 'groups'));
    }

    public function markMadeUp(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'group_id' => ['required', 'exists:dance_groups,id'],
            'date' => ['required', 'date'],
        ]);

        $makeUpDate = Carbon::parse($validated['date']);

        $monthly = app(MonthlyPassService::class);

        if ($monthly->activePassFor($attendance->student, $attendance->date) !== null) {
            $warnings = $monthly->markMadeUp($attendance, $validated['group_id'], $makeUpDate);

            return redirect()->back()
                ->with(empty($warnings) ? 'success' : 'warning', empty($warnings) ? __('Absence marked as made up') : array_unique($warnings));
        }

        $attendance->update([
            'made_up' => true,
            'date_of_made_up' => $makeUpDate,
        ]);

        return redirect()->back()->with('success', __('Absence marked as made up'));
    }
}
