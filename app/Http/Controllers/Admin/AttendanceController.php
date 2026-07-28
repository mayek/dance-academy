<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DanceGroup;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student', 'danceGroup.category', 'recordedBy']);

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

        $existingRecords = collect();
        if ($selectedGroup) {
            $group = DanceGroup::findOrFail($selectedGroup);
            $existingRecords = Attendance::where('dance_group_id', $selectedGroup)
                ->whereDate('date', $selectedDate)
                ->pluck('status', 'student_id');
        }

        return view('admin.attendance.create', compact('groups', 'selectedGroup', 'selectedDate', 'existingRecords'));
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
        $studentIds = $group->students->pluck('id');
        $recordedBy = auth()->id();

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
                    'recorded_by' => $recordedBy,
                ]
            );
        }

        return redirect()->route('admin.attendance.index', [
            'dance_group_id' => $validated['dance_group_id'],
            'date' => $validated['date'],
        ])->with('success', 'Attendance recorded successfully.');
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

        return view('admin.attendance.student-absences', compact('student', 'absences', 'totalAbsences'));
    }
}
