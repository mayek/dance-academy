<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanceCategory;
use App\Models\DanceGroup;
use App\Models\User;
use Illuminate\Http\Request;

class DanceGroupController extends Controller
{
    public function index()
    {
        return view('admin.groups.index');
    }

    public function create()
    {
        $categories = DanceCategory::all();
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.groups.create', compact('categories', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:dance_categories,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'room' => ['nullable', 'string', 'in:górna,dolna'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'class_times' => ['nullable', 'json'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        if ($request->filled('class_times')) {
            $validated['class_times'] = json_decode($request->class_times, true);
        }

        DanceGroup::create($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', __('Dance group created successfully'));
    }

    public function edit(DanceGroup $group)
    {
        $categories = DanceCategory::all();
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.groups.edit', compact('group', 'categories', 'teachers'));
    }

    public function update(Request $request, DanceGroup $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:dance_categories,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'room' => ['nullable', 'string', 'in:górna,dolna'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'class_times' => ['nullable', 'json'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        if ($request->filled('class_times')) {
            $validated['class_times'] = json_decode($request->class_times, true);
        } else {
            $validated['class_times'] = null;
        }

        $group->update($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', __('Dance group updated successfully.'));
    }

    public function destroy(DanceGroup $group)
    {
        $group->delete();
        return redirect()->route('admin.groups.index')
            ->with('success', __('Dance group deleted successfully.'));
    }

    public function assignStudents(DanceGroup $group)
    {
        $enrolledIds = $group->students->pluck('id')->toArray();
        $students = User::where('role', 'student')
            ->orderByRaw('FIELD(id, ' . implode(',', $enrolledIds ?: [0]) . ') DESC')
            ->get();
        return view('admin.groups.assign', compact('group', 'students', 'enrolledIds'));
    }

    public function updateStudents(Request $request, DanceGroup $group)
    {
        $validated = $request->validate([
            'student_ids' => ['array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        $studentIds = $validated['student_ids'] ?? [];
        $group->students()->sync($studentIds);

        $monthlyPasses = app(\App\Services\MonthlyPassService::class);
        $month = now()->startOfMonth();

        foreach (User::whereIn('id', $studentIds)->get() as $student) {
            $obligation = $monthlyPasses->obligationFor($student, $month);

            if ($obligation !== null) {
                $monthlyPasses->refreshTotalHours($obligation);
            } else {
                $monthlyPasses->ensureObligation($student, $month);
            }
        }

        return redirect()->route('admin.groups.index')
            ->with('success', __('Students assigned successfully.'));
    }
}
