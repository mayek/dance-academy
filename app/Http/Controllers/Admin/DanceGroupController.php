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
        $groups = DanceGroup::with(['category', 'teacher', 'students'])->latest()->paginate(10);
        return view('admin.groups.index', compact('groups'));
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
            'schedule' => ['nullable', 'string', 'max:255'],
        ]);

        DanceGroup::create($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Dance group created successfully.');
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
            'schedule' => ['nullable', 'string', 'max:255'],
        ]);

        $group->update($validated);

        return redirect()->route('admin.groups.index')
            ->with('success', 'Dance group updated successfully.');
    }

    public function destroy(DanceGroup $group)
    {
        $group->delete();
        return redirect()->route('admin.groups.index')
            ->with('success', 'Dance group deleted successfully.');
    }

    public function assignStudents(DanceGroup $group)
    {
        $students = User::where('role', 'student')->get();
        $enrolledIds = $group->students->pluck('id')->toArray();
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

        return redirect()->route('admin.groups.index')
            ->with('success', 'Students assigned successfully.');
    }
}
