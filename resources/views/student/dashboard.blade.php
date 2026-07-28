@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('content')
<div class="px-4 sm:px-0">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Dance Groups</h1>

    @forelse($groups as $group)
    <div class="bg-white shadow rounded-lg p-6 mb-4">
        <h2 class="text-lg font-semibold text-gray-900">{{ $group->name }}</h2>
        <p class="text-sm text-gray-500">Category: {{ $group->category->name }}</p>
        <p class="text-sm text-gray-500">Teacher: {{ $group->teacher->name }}</p>
        @if($group->schedule)
            <p class="text-sm text-gray-500">Schedule: {{ $group->schedule }}</p>
        @endif
    </div>
    @empty
    <div class="bg-white shadow rounded-lg p-6">
        <p class="text-gray-500 text-center">You are not enrolled in any dance groups yet.</p>
    </div>
    @endforelse
</div>
@endsection
