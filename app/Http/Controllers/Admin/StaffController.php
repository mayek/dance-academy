<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')->latest()->paginate(10, ['*'], 'teachers_page');
        $receptionists = User::where('role', 'reception')->latest()->paginate(10, ['*'], 'reception_page');

        return view('admin.staff.index', compact('teachers', 'receptionists'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::min(8)->numbers(), 'confirmed'],
            'role' => ['required', 'in:teacher,reception'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.staff.index')
            ->with('success', __('Staff member created successfully.'));
    }

    public function edit(User $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $staff->id],
            'role' => ['required', 'in:teacher,reception'],
        ]);

        $staff->update($validated);

        return redirect()->route('admin.staff.index')
            ->with('success', __('Staff member updated successfully.'));
    }

    public function destroy(User $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')
            ->with('success', __('Staff member deleted successfully.'));
    }
}
