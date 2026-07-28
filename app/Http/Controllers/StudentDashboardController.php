<?php

namespace App\Http\Controllers;

class StudentDashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $groups = $user->enrolledGroups()->with(['category', 'teacher'])->get();

        return view('student.dashboard', compact('groups'));
    }
}
