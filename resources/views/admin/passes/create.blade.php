@extends('layouts.app')
@section('title', __('Add Pass'))

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Add Pass') }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.passes.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       placeholder="{{ __('Optional - auto generated if empty') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm border p-2">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label for="duration" class="block text-sm font-medium text-gray-700">{{ __('Duration') }}</label>
                <select name="duration" id="duration" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm border p-2">
                    <option value="single" {{ old('duration') == 'single' ? 'selected' : '' }}>{{ __('Single Pass') }}</option>
                    <option value="1" {{ old('duration') == '1' ? 'selected' : '' }}>{{ __('1 month') }}</option>
                    <option value="2" {{ old('duration') == '2' ? 'selected' : '' }}>{{ __('2 months') }}</option>
                    <option value="3" {{ old('duration') == '3' ? 'selected' : '' }}>{{ __('3 months') }}</option>
                    <option value="6" {{ old('duration') == '6' ? 'selected' : '' }}>{{ __('6 months') }}</option>
                    <option value="12" {{ old('duration') == '12' ? 'selected' : '' }}>{{ __('12 months') }}</option>
                </select>
                @error('duration') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="mb-6">
                <label for="price" class="block text-sm font-medium text-gray-700">{{ __('Price') }} (PLN)</label>
                <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price') }}" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm border p-2">
                @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Create Pass') }}</button>
                <a href="{{ route('admin.passes.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
