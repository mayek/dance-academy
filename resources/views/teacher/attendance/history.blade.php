@extends('layouts.app')
@section('title', 'Attendance History - ' . $group->name)

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('teacher.attendance.create') }}" class="text-gray-400 hover:text-gray-600">&larr; Back</a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Attendance: {{ $group->name }}</h1>
            <p class="text-sm text-gray-500">{{ $group->category->name }}</p>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recorded By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($attendances as $attendance)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $attendance->student->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($attendance->status === 'present')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Present</span>
                        @elseif($attendance->status === 'absent')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Absent</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Excused</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attendance->recordedBy->full_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-[200px] truncate">{{ $attendance->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No attendance records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $attendances->links() }}</div>
</div>
@endsection
