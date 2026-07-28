@extends('layouts.app')
@section('title', 'Assign Students - ' . $group->name)

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Assign Students</h1>
    <p class="text-gray-600 mb-6">Group: <strong>{{ $group->name }}</strong> ({{ $group->category->name }})</p>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('teacher.update-students', $group) }}">
            @csrf @method('PUT')
            <div class="space-y-2 mb-6">
                @forelse($students as $student)
                <label class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 border border-gray-200 cursor-pointer">
                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                           {{ in_array($student->id, $enrolledIds) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                        <div class="text-xs text-gray-500">{{ $student->email }}</div>
                    </div>
                </label>
                @empty
                <p class="text-sm text-gray-500">No students available.</p>
                @endforelse
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">Save Assignments</button>
                <a href="{{ route('teacher.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
