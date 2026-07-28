@extends('layouts.app')
@section('title', 'Buy Pass')

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Buy Pass</h1>

    @if($groups->isEmpty())
    <div class="bg-white shadow rounded-lg p-6">
        <p class="text-gray-500 text-center">You are not enrolled in any dance groups. Join a group first to buy a pass.</p>
    </div>
    @else
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('student.payments.store') }}">
            @csrf
            <div class="mb-4">
                <label for="dance_group_id" class="block text-sm font-medium text-gray-700">Dance Group</label>
                <select name="dance_group_id" id="dance_group_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    <option value="">Select a group</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ old('dance_group_id', $selectedGroup) == $group->id ? 'selected' : '' }}>
                            {{ $group->name }} ({{ $group->category->name }}) &mdash; {{ $group->teacher->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('dance_group_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Pass Type</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative border rounded-lg p-4 cursor-pointer hover:border-emerald-500 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 transition">
                        <input type="radio" name="pass_type" value="monthly" {{ old('pass_type', 'monthly') == 'monthly' ? 'checked' : '' }} class="sr-only">
                        <div class="text-sm font-semibold text-gray-900">Monthly Pass</div>
                        <div class="text-2xl font-bold text-emerald-600 mt-1">150 &zloty;</div>
                        <div class="text-xs text-gray-500 mt-1">Valid for the current calendar month</div>
                    </label>
                    <label class="relative border rounded-lg p-4 cursor-pointer hover:border-emerald-500 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 transition">
                        <input type="radio" name="pass_type" value="single" {{ old('pass_type') == 'single' ? 'checked' : '' }} class="sr-only">
                        <div class="text-sm font-semibold text-gray-900">Single Class Pass</div>
                        <div class="text-2xl font-bold text-purple-600 mt-1">25 &zloty;</div>
                        <div class="text-xs text-gray-500 mt-1">Valid for one class</div>
                    </label>
                </div>
                @error('pass_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg">Purchase Pass</button>
                <a href="{{ route('student.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
