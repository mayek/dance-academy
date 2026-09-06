<?php

namespace App\Http\Controllers;

class StudentDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $groups = $user->enrolledGroups()->with(['category', 'teacher'])->get();
        $events = $user->events()->with('teacher')->orderBy('date')->orderBy('start_time')->get();

        $currentMonthPass = $user->payments()
            ->active()
            ->where('pass_type', 'monthly')
            ->whereNull('dance_group_id')
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now())
            ->latest('valid_from')
            ->first();

        return view('student.dashboard', compact('groups', 'events', 'currentMonthPass'));
    }
}