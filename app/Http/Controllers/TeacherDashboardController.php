<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $groups = $user->taughtGroups()->with(['category', 'students'])->get();

        $groupIds = $groups->pluck('id');
        $studentIds = $groups->pluck('students.*.id')->flatten()->unique();

        $expiringPasses = Payment::with(['student', 'danceGroup.category'])
            ->where('status', 'active')
            ->where('valid_until', '>=', now())
            ->where('valid_until', '<=', now()->addDays(7))
            ->whereIn('dance_group_id', $groupIds)
            ->orderBy('valid_until')
            ->get();

        return view('teacher.dashboard', compact('groups', 'expiringPasses'));
    }

    public function assignStudents(\App\Models\DanceGroup $group)
    {
        $user = auth()->user();
        abort_unless($group->teacher_id === $user->id, 403);

        $students = \App\Models\User::where('role', 'student')->get();
        $enrolledIds = $group->students->pluck('id')->toArray();

        return view('teacher.assign', compact('group', 'students', 'enrolledIds'));
    }

    public function updateStudents(\Illuminate\Http\Request $request, \App\Models\DanceGroup $group)
    {
        $user = auth()->user();
        abort_unless($group->teacher_id === $user->id, 403);

        $validated = $request->validate([
            'student_ids' => ['array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        $studentIds = $validated['student_ids'] ?? [];
        $group->students()->sync($studentIds);

        return redirect()->route('teacher.dashboard')
            ->with('success', 'Students assigned successfully.');
    }
}
