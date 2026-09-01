<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dance Academy'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-purple-700">{{ __('Dance Academy') }}</span>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-4">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Dashboard') }}</a>
                            <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.staff.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Staff') }}</a>
                            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.students.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Students') }}</a>
                            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.categories.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Categories') }}</a>
                            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.payments.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Payments') }}</a>
                            <a href="{{ route('admin.passes.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.passes.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Passes') }}</a>
                            <a href="{{ route('admin.attendance.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.attendance.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Attendance') }}</a>
                            <a href="{{ route('admin.groups.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.groups.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Groups') }}</a>
                            <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.events.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Events') }}</a>
                        @elseif(auth()->user()->isReception())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Dashboard') }}</a>
                            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.students.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Students') }}</a>
                            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.payments.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Payments') }}</a>
                            <a href="{{ route('admin.passes.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.passes.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Passes') }}</a>
                            <a href="{{ route('admin.attendance.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.attendance.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Attendance') }}</a>
                            <a href="{{ route('admin.groups.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.groups.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Groups') }}</a>
                            <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('admin.events.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Events') }}</a>
                        @elseif(auth()->user()->isTeacher())
                            <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('teacher.dashboard') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('My Groups') }}</a>
                            <a href="{{ route('teacher.events.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('teacher.events.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Events') }}</a>
                            <a href="{{ route('teacher.attendance.create') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('teacher.attendance.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('Attendance') }}</a>
                        @elseif(auth()->user()->isStudent())
                            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('student.dashboard') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('My Groups') }}</a>
                            <a href="{{ route('student.payments.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('student.payments.*') ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ __('My Passes') }}</a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('language.switch', 'en') }}" class="text-sm {{ app()->getLocale() === 'en' ? 'text-purple-700 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">EN</a>
                    <a href="{{ route('language.switch', 'pl') }}" class="text-sm {{ app()->getLocale() === 'pl' ? 'text-purple-700 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">PL</a>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ auth()->user()->full_name }}</a>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ __(ucfirst(auth()->user()->role)) }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 cursor-pointer">{{ __('Logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
