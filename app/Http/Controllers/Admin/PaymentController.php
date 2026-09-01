<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanceGroup;
use App\Models\PassType;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function studentPayments(User $student)
    {
        $payments = Payment::with(['danceGroup.category', 'event', 'recordedBy'])
            ->where('student_id', $student->id)
            ->orderBy('valid_from', 'desc')
            ->paginate(20);

        $studentGroups = $student->enrolledGroups()->with('category')->orderBy('name')->get();
        $groups = $studentGroups->isEmpty()
            ? DanceGroup::with('category')->orderBy('name')->get()
            : $studentGroups;
        $passTypes = PassType::orderBy('type')->orderBy('duration_months')->get();

        return view('admin.payments.student', compact('student', 'payments', 'groups', 'passTypes'));
    }

    public function index()
    {
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

        return view('admin.payments.index', compact('revenue', 'labels'));
    }

    public function create(Request $request)
    {
        $students = User::where('role', 'student')->with('enrolledGroups')->orderBy('first_name')->get();
        $groups = DanceGroup::with(['category', 'teacher'])->orderBy('name')->get();
        $passTypes = PassType::orderBy('type')->orderBy('duration_months')->get();
        $selectedStudent = $request->get('student_id');
        $selectedGroup = $request->get('group_id');

        return view('admin.payments.create', compact('students', 'groups', 'passTypes', 'selectedStudent', 'selectedGroup'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'pass_type_id' => ['required', 'exists:pass_types,id'],
            'valid_from' => ['required', 'date'],
            'total_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'notes' => ['nullable', 'string'],
            'is_paid' => ['nullable', 'boolean'],
        ]);

        $passType = PassType::findOrFail($validated['pass_type_id']);
        $validity = $passType->computeValidity(\Carbon\Carbon::parse($validated['valid_from']));

        $validated['pass_type'] = $passType->type;
        $validated['pass_type_id'] = $passType->id;
        $validated['amount'] = $passType->price;
        $validated['total_hours'] = $request->filled('total_hours')
            ? (float) $validated['total_hours']
            : $passType->hours;
        $validated['valid_from'] = $validity['valid_from'];
        $validated['valid_until'] = $validity['valid_until'];
        $validated['status'] = $validated['valid_until']->lt(now()->startOfDay()) ? 'expired' : 'active';
        $validated['recorded_by'] = auth()->id();
        $validated['is_paid'] = $request->boolean('is_paid', true);

        Payment::create($validated);

        return redirect()->route('admin.students.payments', $validated['student_id'])
            ->with('success', __('Payment recorded successfully.'));
    }

    public function buyPass(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'pass_type_id' => ['required', 'exists:pass_types,id'],
            'total_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'is_paid' => ['nullable', 'boolean'],
        ]);

        $passType = PassType::findOrFail($validated['pass_type_id']);
        $validity = $passType->computeValidity(\Carbon\Carbon::now());

        $validated['pass_type'] = $passType->type;
        $validated['pass_type_id'] = $passType->id;
        $validated['amount'] = $passType->price;
        $validated['total_hours'] = $request->filled('total_hours')
            ? (float) $validated['total_hours']
            : $passType->hours;
        $validated['valid_from'] = $validity['valid_from'];
        $validated['valid_until'] = $validity['valid_until'];
        $validated['status'] = 'active';
        $validated['recorded_by'] = auth()->id();
        $validated['is_paid'] = $request->boolean('is_paid', true);

        Payment::create($validated);

        return redirect()->route('admin.dashboard')
            ->with('success', __('Pass purchased successfully.'));
    }

    public function edit(Payment $payment)
    {
        $students = User::where('role', 'student')->orderBy('first_name')->get();
        $groups = DanceGroup::with(['category', 'teacher'])->orderBy('name')->get();

        return view('admin.payments.edit', compact('payment', 'students', 'groups'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'pass_type' => ['required', 'in:monthly,single'],
            'amount' => ['required', 'numeric', 'min:0'],
            'valid_from' => ['required', 'date'],
            'total_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'used_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'status' => ['required', 'in:active,expired,cancelled'],
            'notes' => ['nullable', 'string'],
            'is_paid' => ['nullable', 'boolean'],
        ]);

        $validated['is_paid'] = $request->boolean('is_paid', $payment->is_paid);

        if ($validated['pass_type'] === 'monthly') {
            $validated['valid_until'] = \Carbon\Carbon::parse($validated['valid_from'])->endOfMonth();
        } else {
            $validated['valid_until'] = \Carbon\Carbon::parse($validated['valid_from'])->endOfDay();
        }

        if (!$request->filled('total_hours')) {
            $validated['total_hours'] = null;
            $validated['used_hours'] = 0;
        }

        if ($validated['total_hours'] !== null) {
            $total = (float) $validated['total_hours'];
            $used = (float) ($request->filled('used_hours') ? $validated['used_hours'] : $payment->used_hours);
            $validated['used_hours'] = min($used, $total);
        }

        $payment->update($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', __('Payment updated successfully.'));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments.index')
            ->with('success', __('Payment deleted successfully.'));
    }
}
