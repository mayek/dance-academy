<?php

namespace App\Http\Controllers;

use App\Models\PassType;
use App\Models\Payment;
use App\Services\MonthlyPassService;
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
        $passTypes = PassType::orderBy('type')->orderBy('duration_months')->get();

        return view('student.payments.create', compact('passTypes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'pass_type_id' => ['required', 'exists:pass_types,id'],
        ]);

        $passType = PassType::findOrFail($validated['pass_type_id']);
        $validity = $passType->computeValidity(Carbon::now());

        $validated['pass_type'] = $passType->type;
        $validated['pass_type_id'] = $passType->id;
        $validated['amount'] = $passType->price;
        $validated['total_hours'] = $passType->hours;

        if ($validated['total_hours'] === null && $passType->isMonthly()) {
            $computed = app(MonthlyPassService::class)->computeHoursBetween(
                $user,
                $validity['valid_from'],
                $validity['valid_until']
            );

            if ($computed > 0) {
                $validated['total_hours'] = $computed;
            }
        }

        $validated['valid_from'] = $validity['valid_from'];
        $validated['valid_until'] = $validity['valid_until'];

        $validated['student_id'] = $user->id;
        $validated['dance_group_id'] = null;
        $validated['recorded_by'] = $user->id;
        $validated['status'] = 'active';

        Payment::create($validated);

        return redirect()->route('student.payments.index')
            ->with('success', __('Pass purchased successfully.'));
    }
}
