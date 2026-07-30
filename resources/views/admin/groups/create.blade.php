@extends('layouts.app')
@section('title', __('Add Dance Group'))

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Add Dance Group') }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.groups.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Group Name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm border p-2">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700">{{ __('Category') }}</label>
                <select name="category_id" id="category_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm border p-2">
                    <option value="">{{ __('Select category') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="teacher_id" class="block text-sm font-medium text-gray-700">{{ __('Teacher') }}</label>
                <select name="teacher_id" id="teacher_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm border p-2">
                    <option value="">{{ __('Select teacher') }}</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                    @endforeach
                </select>
                @error('teacher_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label for="schedule" class="block text-sm font-medium text-gray-700">{{ __('Schedule (description)') }}</label>
                <input type="text" name="schedule" id="schedule" value="{{ old('schedule') }}"
                       placeholder="e.g. Monday & Wednesday, 18:00-19:30"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm border p-2">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Class Times') }}</label>
                <input type="hidden" name="class_times" id="class_times" value="{{ old('class_times', '[]') }}">
                <div class="space-y-2" id="class_times_fields">
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $i => $day)
                    <div class="flex items-center gap-3 p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" data-day="{{ $i + 1 }}" class="day-checkbox rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-sm text-gray-700 w-24">{{ __($day) }}</span>
                        <input type="time" class="start-time rounded-md border-gray-300 text-sm border p-1.5" disabled value="18:00">
                        <span class="text-sm text-gray-400">&ndash;</span>
                        <input type="time" class="end-time rounded-md border-gray-300 text-sm border p-1.5" disabled value="19:30">
                    </div>
                    @endforeach
                </div>
                @error('class_times') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Create Group') }}</button>
                <a href="{{ route('admin.groups.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hidden = document.getElementById('class_times');
    const checkboxes = document.querySelectorAll('.day-checkbox');

    function serialize() {
        const times = [];
        checkboxes.forEach(function (cb) {
            if (!cb.checked) return;
            const row = cb.closest('.flex');
            const start = row.querySelector('.start-time');
            const end = row.querySelector('.end-time');
            times.push({ day: parseInt(cb.dataset.day), start: start.value, end: end.value });
        });
        hidden.value = JSON.stringify(times);
    }

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', function () {
            const row = this.closest('.flex');
            const start = row.querySelector('.start-time');
            const end = row.querySelector('.end-time');
            start.disabled = !this.checked;
            end.disabled = !this.checked;
            serialize();
        });
        const row = cb.closest('.flex');
        row.querySelector('.start-time').addEventListener('change', serialize);
        row.querySelector('.end-time').addEventListener('change', serialize);
    });
});
</script>
@endsection
