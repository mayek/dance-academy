<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PassType;
use App\Models\DanceGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $groups = $user->taughtGroups()->with(['category', 'students.payments'])->get();
        $passTypes = PassType::orderBy('type')->orderBy('duration_months')->get();

        $groupIds = $groups->pluck('id');
        $studentIds = $groups->pluck('students.*.id')->flatten()->unique();

        $expiringPasses = Payment::with(['student', 'danceGroup.category'])
            ->where('status', 'active')
            ->where('valid_until', '>=', now()->startOfDay())
            ->where('valid_until', '<=', now()->addDays(7))
            ->whereIn('student_id', $studentIds)
            ->orderBy('valid_until')
            ->get();

        return view('teacher.dashboard', compact('groups', 'expiringPasses', 'passTypes'));
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

        $monthlyPasses = app(\App\Services\MonthlyPassService::class);
        $month = now()->startOfMonth();

        foreach (\App\Models\User::whereIn('id', $studentIds)->get() as $student) {
            $obligation = $monthlyPasses->obligationFor($student, $month);

            if ($obligation !== null) {
                $monthlyPasses->refreshTotalHours($obligation);
            } else {
                $monthlyPasses->ensureObligation($student, $month);
            }
        }

        return redirect()->route('teacher.dashboard')
            ->with('success', __('Students assigned successfully.'));
    }

    public function storePass(Request $request, DanceGroup $group, User $student)
    {
        $user = auth()->user();
        abort_unless($group->teacher_id === $user->id, 403);
        abort_unless($group->students()->whereKey($student->id)->exists(), 403);

        $validated = $request->validate([
            'pass_type_id' => ['required', 'exists:pass_types,id'],
            'is_paid' => ['nullable', 'boolean'],
        ]);

        $passType = PassType::findOrFail($validated['pass_type_id']);
        $validity = $passType->computeValidity(Carbon::now());

        $validated['pass_type'] = $passType->type;
        $validated['pass_type_id'] = $passType->id;
        $validated['amount'] = $passType->price;
        $validated['total_hours'] = $passType->hours;

        if ($validated['total_hours'] === null && $passType->isMonthly()) {
            $computed = app(\App\Services\MonthlyPassService::class)->computeHoursBetween(
                $student,
                $validity['valid_from'],
                $validity['valid_until']
            );

            if ($computed > 0) {
                $validated['total_hours'] = $computed;
            }
        }

        $validated['valid_from'] = $validity['valid_from'];
        $validated['valid_until'] = $validity['valid_until'];

        $validated['student_id'] = $student->id;
        $validated['dance_group_id'] = null;
        $validated['recorded_by'] = $user->id;
        $validated['status'] = 'active';
        $validated['is_paid'] = $request->boolean('is_paid', true);

        Payment::create($validated);

        return redirect()->route('teacher.dashboard')
            ->with('success', __('Pass purchased successfully.'));
    }
}
