@extends('layouts.app')
@section('title', __('Record Attendance'))

@section('content')
<div class="px-4 sm:px-0 max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Record Attendance') }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('admin.attendance.create') }}" class="mb-6 pb-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="group_id" class="block text-sm font-medium text-gray-700">{{ __('Dance Group') }}</label>
                    <select name="group_id" id="group_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                        <option value="">{{ __('Select group') }}</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ $selectedGroup == $group->id ? 'selected' : '' }}>{{ $group->name }} ({{ $group->category->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">{{ __('Date') }}</label>
                    <input type="date" name="date" id="date" value="{{ $selectedDate }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border p-2">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('Load Students') }}</button>
                </div>
            </div>
        </form>

        @if($selectedGroup)
            @php
                $group = $groups->firstWhere('id', $selectedGroup);
            @endphp
            @if($group && $group->students->isNotEmpty())
            <form method="POST" action="{{ route('admin.attendance.store') }}">
                @csrf
                <input type="hidden" name="dance_group_id" value="{{ $selectedGroup }}">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $group->name }} &mdash; {{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}</h2>
                </div>

                <div class="space-y-3">
                    @foreach($group->students as $student)
                    <div class="flex items-center gap-4 p-3 rounded-lg border border-gray-200 hover:bg-gray-50">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $student->email }}</div>
                        </div>
                        <div class="w-40">
                            <select name="statuses[{{ $student->id }}]"
                                    class="block w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-1.5">
                                <option value="present" {{ ($existingRecords[$student->id] ?? 'present') === 'present' ? 'selected' : '' }}>{{ __('Present') }}</option>
                                <option value="absent" {{ ($existingRecords[$student->id] ?? '') === 'absent' ? 'selected' : '' }}>{{ __('Absent') }}</option>
                                <option value="excused" {{ ($existingRecords[$student->id] ?? '') === 'excused' ? 'selected' : '' }}>{{ __('Excused') }}</option>
                            </select>
                        </div>
                        <div class="w-48">
                            <input type="text" name="notes[{{ $student->id }}]" placeholder="{{ __('Note (optional)') }}"
                                   class="w-full rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border p-1.5">
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-6 flex space-x-3">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Save Attendance') }}</button>
                    <a href="{{ route('admin.attendance.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
                </div>
            </form>
            @elseif($group)
            <p class="text-gray-500 text-center">{{ __('No students enrolled in this group.') }}</p>
            @endif
        @else
            <p class="text-gray-400 text-center">{{ __('Select a group and date above to record attendance.') }}</p>
        @endif
    </div>
</div>
@endsection
