<?php

namespace App\Http\Controllers;

class StudentDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $groups = $user->enrolledGroups()->with(['category', 'teacher'])->get();
        $events = $user->events()->with('teacher')->orderBy('date')->orderBy('start_time')->get();

        $activePassByGroup = $user->payments()
            ->active()
            ->where('dance_group_id', '!=', null)
            ->get()
            ->keyBy('dance_group_id');

        return view('student.dashboard', compact('groups', 'events', 'activePassByGroup'));
    }
}
