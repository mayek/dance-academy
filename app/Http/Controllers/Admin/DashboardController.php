<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanceCategory;
use App\Models\DanceGroup;
use App\Models\PassType;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'teachers' => User::where('role', 'teacher')->count(),
            'reception' => User::where('role', 'reception')->count(),
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
            ->whereNull('event_id')
            ->where('valid_until', '>=', now()->startOfDay())
            ->where('valid_until', '<=', now()->addDays(7))
            ->orderBy('valid_until')
            ->get();

        $months = 12;
        $revenue = [];
        $labels = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $total = Payment::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->where('status', '!=', 'cancelled')
                ->sum('amount');

            $revenue[] = (float) $total;
            $labels[] = $date->translatedFormat('M Y');
        }

        $passTypes = PassType::orderBy('type')->orderBy('duration_months')->get();
        $students = User::where('role', 'student')->with('enrolledGroups')->orderBy('first_name')->get();
        $groups = DanceGroup::with('category')->orderBy('name')->get();

        return view('admin.dashboard', compact('stats', 'expiringPasses', 'revenue', 'labels', 'passTypes', 'students', 'groups'));
    }
}
