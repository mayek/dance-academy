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
            <div class="mb-6">
                <label for="hours" class="block text-sm font-medium text-gray-700">{{ __('Total hours') }}</label>
                <input type="number" name="hours" id="hours" step="0.5" min="0" value="{{ old('hours') }}"
                       placeholder="{{ __('e.g. 2h/week × 4 weeks = 8') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm border p-2">
                @error('hours') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">{{ __('Total hours for the entire pass duration. E.g. 2h/week × 4 weeks = 8.') }}</p>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">{{ __('Create Pass') }}</button>
                <a href="{{ route('admin.passes.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mt-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">{{ __('How passes work') }}</h2>

        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Manually added passes') }}</h3>
            <p class="text-sm text-gray-600 mb-3">
                {{ __('A pass created here defines a fixed pool of hours that can be used during its validity period. When you sell this pass to a student, each attended lesson deducts its duration from the pool.') }}
            </p>
            <ul class="text-sm text-gray-600 list-disc pl-5 space-y-1">
                <li>{{ __('The “Total hours” value is the whole pool for the entire pass period, not per week or per month.') }}</li>
                <li>{{ __('The pool lasts as long as the chosen duration (e.g. 1, 3, 6 or 12 months).') }}</li>
                <li>{{ __('Once the pool is used up, the pass no longer covers lessons, even if the period has not ended.') }}</li>
            </ul>
            <h4 class="text-sm font-semibold text-gray-700 mt-3 mb-1">{{ __('Examples') }}</h4>
            <ul class="text-sm text-gray-600 list-disc pl-5 space-y-1">
                <li>{{ __('2 hours per week, 1 month → enter “Total hours” = 8 (2h × 4 weeks) or 10 (2h × 5 weeks).') }}</li>
                <li>{{ __('1.5 hours per week, 3 months → enter “Total hours” = 18 (1.5h × 4 weeks × 3 months) or 22.5 (1.5h × 5 weeks × 3 months).') }}</li>
            </ul>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Automatic monthly passes') }}</h3>
            <p class="text-sm text-gray-600 mb-3">
                {{ __('If you use the automatic monthly pass system, you do NOT enter hours here. The system calculates the number of hours automatically from the student’s real schedule for each calendar month: it sums up every scheduled lesson in the month (excluding cancelled ones) times its duration.') }}
            </p>
            <ul class="text-sm text-gray-600 list-disc pl-5 space-y-1">
                <li>{{ __('Hours are recalculated per month, so months with 4 vs 5 weeks are handled automatically.') }}</li>
                <li>{{ __('The pass is linked to the student and not to a manually set pool here.') }}</li>
                <li>{{ __('You do not need to create these pass types here – they are generated automatically for enrolled students.') }}</li>
            </ul>
            <h4 class="text-sm font-semibold text-gray-700 mt-3 mb-1">{{ __('Example') }}</h4>
            <ul class="text-sm text-gray-600 list-disc pl-5 space-y-1">
                <li>{{ __('A group meets 1 hour every Monday. In a 4-week month the automatic pass has 4 hours, in a 5-week month it has 5 hours.') }}</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const duration = document.getElementById('duration');
    const hours = document.getElementById('hours');

    function toggleHours() {
        hours.disabled = duration.value === 'single';
        if (hours.disabled) hours.value = '';
    }

    duration.addEventListener('change', toggleHours);
    toggleHours();
});
</script>
@endsection
