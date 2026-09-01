<?php

namespace App\Http\Controllers;

use App\Models\DanceGroup;
use App\Models\PassType;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentPaymentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $payments = $user->payments()
            ->with('danceGroup.category', 'event')
            ->latest()
            ->paginate(15);

        return view('student.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $groups = $user->enrolledGroups()->with(['category', 'teacher'])->get();
        $passTypes = PassType::orderBy('type')->orderBy('duration_months')->get();

        $selectedGroup = $request->get('group_id');

        return view('student.payments.create', compact('groups', 'passTypes', 'selectedGroup'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'pass_type_id' => ['required', 'exists:pass_types,id'],
        ]);

        $group = DanceGroup::findOrFail($validated['dance_group_id']);
        abort_unless($user->enrolledGroups()->where('dance_group_id', $group->id)->exists(), 403);

        $passType = PassType::findOrFail($validated['pass_type_id']);
        $validity = $passType->computeValidity(Carbon::now());

        $validated['pass_type'] = $passType->type;
        $validated['pass_type_id'] = $passType->id;
        $validated['amount'] = $passType->price;
        $validated['valid_from'] = $validity['valid_from'];
        $validated['valid_until'] = $validity['valid_until'];

        $validated['student_id'] = $user->id;
        $validated['recorded_by'] = $user->id;
        $validated['status'] = 'active';

        Payment::create($validated);

        return redirect()->route('student.payments.index')
            ->with('success', __('Pass purchased successfully.'));
    }
}
