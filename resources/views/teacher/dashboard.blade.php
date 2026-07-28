@extends('layouts.app')
@section('title', 'Teacher Dashboard')

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Dance Groups</h1>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif

    @if($expiringPasses->isNotEmpty())
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse"></div>
            <h2 class="text-lg font-semibold text-gray-900">Expiring Passes (next 7 days)</h2>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">{{ $expiringPasses->count() }}</span>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-amber-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pass Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Left</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($expiringPasses as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->student->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->danceGroup->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($payment->isMonthly())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Monthly</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">Single</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->valid_until->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php $days = (int) now()->diffInDays($payment->valid_until, false); @endphp
                            @if($days <= 1)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ $days }} day</span>
                            @elseif($days <= 3)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">{{ $days }} days</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">{{ $days }} days</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @forelse($groups as $group)
    <div class="bg-white shadow rounded-lg p-6 mb-4">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $group->name }}</h2>
                <p class="text-sm text-gray-500">Category: {{ $group->category->name }}</p>
                @if($group->schedule)
                    <p class="text-sm text-gray-500">Schedule: {{ $group->schedule }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('teacher.attendance.create', ['group_id' => $group->id]) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium py-2 px-4 rounded-lg">Record Attendance</a>
                <a href="{{ route('teacher.attendance.history', $group) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg">History</a>
                <a href="{{ route('teacher.assign', $group) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-4 rounded-lg">Assign Students</a>
            </div>
        </div>
        <div class="mt-4">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Enrolled Students ({{ $group->students->count() }})</h3>
            @if($group->students->isEmpty())
                <p class="text-sm text-gray-400">No students enrolled yet.</p>
            @else
                <ul class="divide-y divide-gray-100">
                    @foreach($group->students as $student)
                    <li class="py-2 flex justify-between">
                        <span class="text-sm text-gray-900">{{ $student->full_name }}</span>
                        <span class="text-xs text-gray-500">{{ $student->email }}</span>
                    </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white shadow rounded-lg p-6">
        <p class="text-gray-500 text-center">No groups assigned to you yet.</p>
    </div>
    @endforelse
</div>
@endsection
