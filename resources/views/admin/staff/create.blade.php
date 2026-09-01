@extends('layouts.app')
@section('title', __('Add Staff Member'))

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Add Staff Member') }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.staff.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">{{ __('Role') }}</label>
                <select name="role" id="role" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2">
                    <option value="teacher" {{ old('role') === 'teacher' ? 'selected' : '' }}>{{ __('Instructor') }}</option>
                    <option value="reception" {{ old('role') === 'reception' ? 'selected' : '' }}>{{ __('Reception') }}</option>
                </select>
                @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                <input type="password" name="password" id="password" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2">
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Create Staff Member') }}</button>
                <a href="{{ route('admin.staff.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
