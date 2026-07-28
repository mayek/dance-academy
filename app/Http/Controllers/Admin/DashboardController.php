<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanceCategory;
use App\Models\DanceGroup;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'categories' => DanceCategory::count(),
            'groups' => DanceGroup::count(),
            'active_passes' => Payment::active()->count(),
            'monthly_revenue' => Payment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('status', '!=', 'cancelled')
                ->sum('amount'),
        ];

        $expiringPasses = Payment::with(['student', 'danceGroup.category'])
            ->where('status', 'active')
            ->where('valid_until', '>=', now())
            ->where('valid_until', '<=', now()->addDays(7))
            ->orderBy('valid_until')
            ->get();

        return view('admin.dashboard', compact('stats', 'expiringPasses'));
    }
}
