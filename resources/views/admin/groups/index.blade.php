@extends('layouts.app')
@section('title', __('Manage Dance Groups'))

@section('content')
<div class="px-4 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Dance Groups') }}</h1>
        <a href="{{ route('admin.groups.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2 px-4 rounded-lg">{{ __('+ Add Group') }}</a>
    </div>
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Weekly Schedule') }}</h2>
        <livewire:group-calendar />
    </div>

    <livewire:group-search />
</div>
@endsection
