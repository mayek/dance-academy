@extends('layouts.app')
@section('title', __('Manage Students'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Students') }}</h1>
        <a href="{{ route('admin.students.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('+ Add Student') }}</a>
    </div>

    <livewire:student-search />
</div>
@endsection
