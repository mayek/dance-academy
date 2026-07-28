<?php

namespace App\Http\Controllers;

use App\Models\DanceGroup;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentPaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $payments = $user->payments()
            ->with('danceGroup.category')
            ->latest()
            ->paginate(15);

        return view('student.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $groups = $user->enrolledGroups()->with(['category', 'teacher'])->get();

        $selectedGroup = $request->get('group_id');

        return view('student.payments.create', compact('groups', 'selectedGroup'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'pass_type' => ['required', 'in:monthly,single'],
        ]);

        $group = DanceGroup::findOrFail($validated['dance_group_id']);
        abort_unless($user->enrolledGroups()->where('dance_group_id', $group->id)->exists(), 403);

        if ($validated['pass_type'] === 'monthly') {
            $validated['amount'] = 150.00;
            $validated['valid_from'] = Carbon::now()->startOfMonth();
            $validated['valid_until'] = Carbon::now()->endOfMonth();
        } else {
            $validated['amount'] = 25.00;
            $validated['valid_from'] = Carbon::now();
            $validated['valid_until'] = Carbon::now();
        }

        $validated['student_id'] = $user->id;
        $validated['status'] = 'active';

        Payment::create($validated);

        return redirect()->route('student.payments.index')
            ->with('success', 'Pass purchased successfully.');
    }
}
