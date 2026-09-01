@extends('layouts.app')
@section('title', __('My Profile'))

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('My Profile') }}</h1>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf @method('PUT')
            @if($user->first_name)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                        @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                        @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email Address') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            @if($user->isStudent())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700">{{ __('Date of Birth') }}</label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                               value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                        @error('date_of_birth') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">{{ __('Phone Number') }}</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                        @error('phone_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="parent_phone_number" class="block text-sm font-medium text-gray-700">{{ __("Parent's Phone Number") }}</label>
                        <input type="text" name="parent_phone_number" id="parent_phone_number"
                               value="{{ old('parent_phone_number', $user->parent_phone_number) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                        @error('parent_phone_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label for="tournament_group" class="block text-sm font-medium text-gray-700">{{ __('Tournament Group') }}</label>
                    <input type="text" name="tournament_group" id="tournament_group"
                           value="{{ old('tournament_group', $user->tournament_group) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('tournament_group') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Update Profile') }}</button>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Change Password') }}</h2>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf @method('PUT')
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700">{{ __('Current Password') }}</label>
                <input type="password" name="current_password" id="current_password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
                    <input type="password" name="password" id="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                </div>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Update Password') }}</button>
        </form>
    </div>
</div>
@endsection
