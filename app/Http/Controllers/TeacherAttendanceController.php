<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\DanceGroup;
use Illuminate\Http\Request;

class TeacherAttendanceController extends Controller
{
    public function create(Request $request)
    {
        $user = auth()->user();
        $groups = $user->taughtGroups()->with(['category', 'students'])->orderBy('name')->get();
        $selectedGroup = $request->get('group_id');
        $selectedDate = $request->get('date', now()->format('Y-m-d'));

        $existingRecords = collect();
        if ($selectedGroup) {
            $group = $groups->firstWhere('id', $selectedGroup);
            if ($group) {
                $existingRecords = Attendance::where('dance_group_id', $selectedGroup)
                    ->whereDate('date', $selectedDate)
                    ->pluck('status', 'student_id');
            }
        }

        return view('teacher.attendance.create', compact('groups', 'selectedGroup', 'selectedDate', 'existingRecords'));
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

        $studentIds = $group->students->pluck('id');

        foreach ($validated['statuses'] as $studentId => $status) {
            if (!in_array((int) $studentId, $studentIds->toArray())) {
                continue;
            }

            Attendance::updateOrCreate(
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
        }

        return redirect()->route('teacher.attendance.create', [
            'group_id' => $validated['dance_group_id'],
            'date' => $validated['date'],
        ])->with('success', 'Attendance recorded successfully.');
    }

    public function history(DanceGroup $group)
    {
        $user = auth()->user();
        abort_unless($group->teacher_id === $user->id, 403);

        $attendances = Attendance::with(['student', 'recordedBy'])
            ->where('dance_group_id', $group->id)
            ->orderBy('date', 'desc')
            ->orderBy('student_id')
            ->paginate(20);

        return view('teacher.attendance.history', compact('group', 'attendances'));
    }
}
