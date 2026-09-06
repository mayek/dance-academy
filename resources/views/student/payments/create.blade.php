@extends('layouts.app')
@section('title', __('Buy Pass'))

@section('content')
<div class="px-4 sm:px-0 max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Buy Pass') }}</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('student.payments.store') }}">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Pass Type') }}</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($passTypes as $passType)
                    <label class="relative border rounded-lg p-4 cursor-pointer hover:border-emerald-500 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 transition">
                        <input type="radio" name="pass_type_id" value="{{ $passType->id }}" {{ old('pass_type_id') == $passType->id ? 'checked' : '' }} class="sr-only">
                        <div class="text-sm font-semibold text-gray-900">{{ $passType->display_name }}</div>
                        <div class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($passType->price, 2) }} zł</div>
                        <div class="text-xs text-gray-500 mt-1">
                            @if($passType->isSingle())
                                {{ __('Valid for one class') }}
                            @else
                                {{ __('Valid for') }} {{ $passType->duration_label }}
                            @endif
                        </div>
                        @if($passType->hours !== null)
                            <div class="text-xs font-medium text-emerald-600 mt-1">{{ $passType->hours }}h {{ __('included') }}</div>
                        @endif
                    </label>
                    @endforeach
                </div>
                @error('pass_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Purchase Pass') }}</button>
                <a href="{{ route('student.payments.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
