<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Create a new student account') }} - {{ __('Dance Academy') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-purple-700">{{ __('Dance Academy') }}</h1>
            <p class="mt-2 text-gray-600">{{ __('Create a new student account') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-8">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">{{ __('First name') }}</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required autofocus
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2" placeholder="e.g. Zofia">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">{{ __('Last name') }}</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2" placeholder="e.g. Wiśniewska">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2" placeholder="e.g. zofia@student.pl">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
                        <input type="password" name="password" id="password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm password') }}</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">{{ __('Date of birth') }}</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2">
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">{{ __('Phone number') }}</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2" placeholder="Optional">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="parent_phone_number" class="block text-sm font-medium text-gray-700">{{ __('Parent phone') }}</label>
                        <input type="text" name="parent_phone_number" id="parent_phone_number" value="{{ old('parent_phone_number') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm border p-2" placeholder="Optional">
                        @error('parent_phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    {{ __('Create Account') }}
                </button>

                <p class="mt-4 text-center text-sm text-gray-500">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-800 font-medium">{{ __('Sign in') }}</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
