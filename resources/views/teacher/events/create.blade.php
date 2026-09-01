@extends('layouts.app')
@section('title', __('Add Event'))

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Add Event') }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('teacher.events.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Event Name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-gray-700">{{ __('Date') }}</label>
                <input type="date" name="date" id="date" value="{{ old('date') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="room" class="block text-sm font-medium text-gray-700">{{ __('Room') }}</label>
                <select name="room" id="room"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    <option value="">{{ __('Select room') }}</option>
                    <option value="górna" {{ old('room') == 'górna' ? 'selected' : '' }}>{{ __('Upper') }}</option>
                    <option value="dolna" {{ old('room') == 'dolna' ? 'selected' : '' }}>{{ __('Lower') }}</option>
                </select>
                @error('room') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">{{ __('Start Time') }}</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    @error('start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">{{ __('End Time') }}</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm border p-2">
                    @error('end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Create Event') }}</button>
                <a href="{{ route('teacher.events.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
