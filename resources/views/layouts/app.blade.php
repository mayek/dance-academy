@php
    $user = auth()->user();
    if ($user->isAdmin()) {
        $navLinks = [
            ['route' => 'admin.dashboard', 'label' => __('Dashboard'), 'pattern' => 'admin.dashboard'],
            ['route' => 'admin.staff.index', 'label' => __('Staff'), 'pattern' => 'admin.staff.*'],
            ['route' => 'admin.students.index', 'label' => __('Students'), 'pattern' => 'admin.students.*'],
            ['route' => 'admin.categories.index', 'label' => __('Categories'), 'pattern' => 'admin.categories.*'],
            ['route' => 'admin.payments.index', 'label' => __('Payments'), 'pattern' => 'admin.payments.*'],
            ['route' => 'admin.passes.index', 'label' => __('Passes'), 'pattern' => 'admin.passes.*'],
            ['route' => 'admin.attendance.index', 'label' => __('Attendance'), 'pattern' => 'admin.attendance.*'],
            ['route' => 'admin.sessions.index', 'label' => __('Sessions'), 'pattern' => 'admin.sessions.*'],
            ['route' => 'admin.groups.index', 'label' => __('Groups'), 'pattern' => 'admin.groups.*'],
            ['route' => 'admin.events.index', 'label' => __('Events'), 'pattern' => 'admin.events.*'],
        ];
    } elseif ($user->isReception()) {
        $navLinks = [
            ['route' => 'admin.dashboard', 'label' => __('Dashboard'), 'pattern' => 'admin.dashboard'],
            ['route' => 'admin.students.index', 'label' => __('Students'), 'pattern' => 'admin.students.*'],
            ['route' => 'admin.payments.index', 'label' => __('Payments'), 'pattern' => 'admin.payments.*'],
            ['route' => 'admin.passes.index', 'label' => __('Passes'), 'pattern' => 'admin.passes.*'],
            ['route' => 'admin.attendance.index', 'label' => __('Attendance'), 'pattern' => 'admin.attendance.*'],
            ['route' => 'admin.sessions.index', 'label' => __('Sessions'), 'pattern' => 'admin.sessions.*'],
            ['route' => 'admin.groups.index', 'label' => __('Groups'), 'pattern' => 'admin.groups.*'],
            ['route' => 'admin.events.index', 'label' => __('Events'), 'pattern' => 'admin.events.*'],
        ];
    } elseif ($user->isTeacher()) {
        $navLinks = [
            ['route' => 'teacher.dashboard', 'label' => __('My Groups'), 'pattern' => 'teacher.dashboard'],
            ['route' => 'teacher.events.index', 'label' => __('Events'), 'pattern' => 'teacher.events.*'],
            ['route' => 'teacher.attendance.create', 'label' => __('Attendance'), 'pattern' => 'teacher.attendance.*'],
        ];
    } elseif ($user->isStudent()) {
        $navLinks = [
            ['route' => 'student.dashboard', 'label' => __('My Groups'), 'pattern' => 'student.dashboard'],
            ['route' => 'student.payments.index', 'label' => __('My Passes'), 'pattern' => 'student.payments.*'],
        ];
    } else {
        $navLinks = [];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dance Academy'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-purple-700">{{ __('Dance Academy') }}</span>
                    <div class="hidden lg:ml-8 lg:flex lg:space-x-4">
                        @foreach($navLinks as $link)
                            <a href="{{ route($link['route']) }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs($link['pattern']) ? 'text-gray-900 underline' : 'text-gray-500 hover:text-gray-900' }}">{{ $link['label'] }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <a href="{{ route('help.index') }}" title="{{ __('Help / FAQ') }}" class="text-gray-500 hover:text-purple-700" aria-label="{{ __('Help / FAQ') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                    <a href="{{ route('language.switch', 'en') }}" class="text-sm {{ app()->getLocale() === 'en' ? 'text-purple-700 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">EN</a>
                    <a href="{{ route('language.switch', 'pl') }}" class="text-sm {{ app()->getLocale() === 'pl' ? 'text-purple-700 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">PL</a>
                    <a href="{{ route('profile.edit') }}" class="hidden sm:block text-sm text-gray-600 hover:text-gray-900">{{ $user->full_name }}</a>
                    <span class="hidden md:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">{{ __(ucfirst($user->role)) }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 cursor-pointer">{{ __('Logout') }}</button>
                    </form>
                    <button id="mobile-menu-button" type="button" aria-expanded="false" aria-controls="mobile-menu" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100">
                        <span class="sr-only">{{ __('Menu') }}</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-200 bg-white">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs($link['pattern']) ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">{{ $link['label'] }}</a>
                @endforeach
                <div class="pt-3 mt-3 border-t border-gray-200">
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-gray-600 hover:text-gray-900">{{ $user->full_name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-500 hover:text-gray-700 cursor-pointer">{{ __('Logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
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