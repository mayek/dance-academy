<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class StudentController extends Controller
{
    public function index()
    {
        return view('admin.students.index');
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['nullable', 'string', Password::min(8)->numbers(), 'confirmed'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'phone_number' => ['required', 'string', 'max:20'],
            'parent_phone_number' => ['nullable', 'string', 'max:20'],
            'tournament_group' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:dance_groups,id'],
        ]);

        $name = $validated['first_name'] . ' ' . $validated['last_name'];

        if (empty($validated['password'])) {
            $validated['password'] = $validated['phone_number'];
        }

        $groupIds = $validated['group_ids'] ?? [];
        unset($validated['group_ids']);

        $student = User::create([
            ...$validated,
            'name' => $name,
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        $student->enrolledGroups()->sync($groupIds);

        return redirect()->route('admin.students.index')
            ->with('success', __('Student created successfully.'));
    }

    public function edit(User $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'password' => ['nullable', 'string', Password::min(8)->numbers(), 'confirmed'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'phone_number' => ['required', 'string', 'max:20'],
            'parent_phone_number' => ['nullable', 'string', 'max:20'],
            'tournament_group' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => ['integer', 'exists:dance_groups,id'],
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $groupIds = $validated['group_ids'] ?? [];
        unset($validated['group_ids']);

        $student->update($validated);
        $student->enrolledGroups()->sync($groupIds);

        return redirect()->route('admin.students.index')
            ->with('success', __('Student updated successfully.'));
    }

    public function destroy(User $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', __('Student deleted successfully.'));
    }
}
