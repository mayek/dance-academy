<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanceGroup;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'danceGroup.category']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('dance_group_id')) {
            $query->where('dance_group_id', $request->dance_group_id);
        }
        if ($request->filled('pass_type')) {
            $query->where('pass_type', $request->pass_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(15);
        $students = User::where('role', 'student')->orderBy('first_name')->get();
        $groups = DanceGroup::with('category')->orderBy('name')->get();

        return view('admin.payments.index', compact('payments', 'students', 'groups'));
    }

    public function create(Request $request)
    {
        $students = User::where('role', 'student')->orderBy('first_name')->get();
        $groups = DanceGroup::with(['category', 'teacher'])->orderBy('name')->get();
        $selectedStudent = $request->get('student_id');
        $selectedGroup = $request->get('group_id');

        return view('admin.payments.create', compact('students', 'groups', 'selectedStudent', 'selectedGroup'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'dance_group_id' => ['required', 'exists:dance_groups,id'],
            'pass_type' => ['required', 'in:monthly,single'],
            'amount' => ['required', 'numeric', 'min:0'],
            'valid_from' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['pass_type'] === 'monthly') {
            $validated['valid_until'] = \Carbon\Carbon::parse($validated['valid_from'])->endOfMonth();
        } else {
            $validated['valid_until'] = $validated['valid_from'];
        }

        $validated['status'] = $validated['valid_until']->isPast() ? 'expired' : 'active';

        Payment::create($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment recorded successfully.');
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
            'status' => ['required', 'in:active,expired,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['pass_type'] === 'monthly') {
            $validated['valid_until'] = \Carbon\Carbon::parse($validated['valid_from'])->endOfMonth();
        } else {
            $validated['valid_until'] = $validated['valid_from'];
        }

        $payment->update($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
