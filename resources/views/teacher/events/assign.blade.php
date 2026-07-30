@extends('layouts.app')
@section('title', __('Assign Students to ') . $event->name)

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ __('Assign Students') }}</h1>
    <p class="text-gray-600 mb-6">{{ __('Event') }}: <strong>{{ $event->name }}</strong> &mdash; {{ $event->date->format('d.m.Y') }}, {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</p>

    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-4">
            <input type="text" id="assign_search" placeholder="{{ __('Search by name, phone, email...') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
        </div>

        <form method="POST" action="{{ route('teacher.events.update-students', $event) }}">
            @csrf @method('PUT')
            <div class="space-y-2 mb-6" id="assign_student_list">
                @forelse($students as $student)
                <label class="assign-student-item flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 border border-gray-200 cursor-pointer"
                       data-name="{{ mb_strtolower($student->full_name) }}"
                       data-first="{{ mb_strtolower($student->first_name) }}"
                       data-last="{{ mb_strtolower($student->last_name) }}"
                       data-email="{{ mb_strtolower($student->email) }}"
                       data-phone="{{ $student->phone_number ?? '' }}"
                       data-parent-phone="{{ $student->parent_phone_number ?? '' }}">
                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                           {{ in_array($student->id, $enrolledIds) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $student->email }}
                            @if($student->phone_number) &middot; {{ $student->phone_number }} @endif
                            @if($student->parent_phone_number) &middot; {{ $student->parent_phone_number }} @endif
                        </div>
                    </div>
                </label>
                @empty
                <p class="text-sm text-gray-500">{{ __('No students available.') }}</p>
                @endforelse
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Save Assignments') }}</button>
                <a href="{{ route('teacher.events.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('assign_search');
    const items = document.querySelectorAll('.assign-student-item');

    input.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        items.forEach(function (item) {
            const searchable = [
                item.dataset.name,
                item.dataset.first,
                item.dataset.last,
                item.dataset.email,
                item.dataset.phone,
                item.dataset.parentPhone
            ].filter(Boolean).join(' ');
            item.style.display = !q || searchable.includes(q) ? '' : 'none';
        });
    });
});
</script>
@endsection
