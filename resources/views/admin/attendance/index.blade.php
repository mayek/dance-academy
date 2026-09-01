@extends('layouts.app')
@section('title', __('Attendance Records'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Attendance Records') }}</h1>
        <a href="{{ route('admin.attendance.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('+ Record Attendance') }}</a>
    </div>

    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="dance_group_id" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Group') }}</label>
                <select name="dance_group_id" id="dance_group_id" class="w-full rounded-md border-gray-300 text-sm border p-2">
                    <option value="">{{ __('All groups') }}</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('dance_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Date') }}</label>
                <input type="date" name="date" id="date" value="{{ request('date') }}" class="w-full rounded-md border-gray-300 text-sm border p-2">
            </div>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 text-sm border p-2">
                    <option value="">{{ __('All statuses') }}</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>{{ __('Present') }}</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>{{ __('Absent') }}</option>
                    <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>{{ __('Excused') }}</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('Filter') }}</button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Student') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Group') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Lesson Duration') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Remaining Hours') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Recorded By') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Notes') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($attendances as $attendance)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $attendance->student->full_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->danceGroup->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->lessonHours() }}h</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($attendance->status === 'present')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">{{ __('Present') }}</span>
                        @elseif($attendance->status === 'absent')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ __('Absent') }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">{{ __('Excused') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($attendance->payment)
                            @php $remaining = $attendance->payment->remainingHours(); @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $remaining > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $remaining }} / {{ $attendance->payment->total_hours }}h
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->recordedBy->full_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-[200px] truncate">{{ $attendance->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('No attendance records found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $attendances->withQueryString()->links() }}</div>
</div>
@endsection
