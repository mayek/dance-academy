<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ];

        if ($user->first_name) {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
        } else {
            $rules['name'] = ['required', 'string', 'max:255'];
        }

        if ($user->isStudent()) {
            $rules['date_of_birth'] = ['required', 'date', 'before:today'];
            $rules['parent_phone_number'] = ['nullable', 'string', 'max:20'];
            $rules['tournament_group'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        if (isset($validated['first_name'])) {
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        }

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', __('Profile updated successfully.'));
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::min(8)->numbers(), 'confirmed'],
        ]);

        $user->update(['password' => $validated['password']]);

        return redirect()->route('profile.edit')
            ->with('success', __('Password changed successfully.'));
    }
}
