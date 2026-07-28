<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')->latest()->paginate(10);
        return view('admin.students.index', compact('students'));
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'pesel' => ['required', 'string', 'size:11', 'unique:users,pesel'],
            'phone_number' => ['required', 'string', 'max:20'],
            'parent_phone_number' => ['nullable', 'string', 'max:20'],
            'tournament_group' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $name = $validated['first_name'] . ' ' . $validated['last_name'];

        User::create([
            ...$validated,
            'name' => $name,
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
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
            'date_of_birth' => ['required', 'date', 'before:today'],
            'pesel' => ['required', 'string', 'size:11', 'unique:users,pesel,' . $student->id],
            'phone_number' => ['required', 'string', 'max:20'],
            'parent_phone_number' => ['nullable', 'string', 'max:20'],
            'tournament_group' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(User $student)
    {
        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
