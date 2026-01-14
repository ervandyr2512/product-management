<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Platform Konsultasi Kesehatan Mental</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="antialiased bg-white dark:bg-gray-900 transition-colors duration-200">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 fixed w-full top-0 z-50 transition-colors duration-200" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="/" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                            Teman Bicara
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="/" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                            {{ __('messages.home') }}
                        </a>
                        <a href="{{ route('professionals.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                            {{ __('messages.professionals') }}
                        </a>
                        <a href="{{ route('articles.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                            {{ __('messages.articles') }}
                        </a>
                        <a href="{{ route('about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                            {{ __('messages.about_us') }}
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                            {{ __('messages.contact') }}
                        </a>
                        @auth
                            @if(Auth::user()->role === 'professional')
                                <a href="{{ route('professional.schedules.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out">
                                    {{ __('messages.my_schedule') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                    @auth
                        <!-- Appointments Icon -->
                        <a href="{{ route('appointments.index') }}" class="relative hover:text-purple-600 dark:hover:text-purple-400 transition" title="Janji Temu">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            @php
                                $appointmentsCount = Auth::user()->appointments()
                                    ->where('status', 'confirmed')
                                    ->whereHas('schedule', function($q) {
                                        $q->where('date', '>=', now()->toDateString());
                                    })
                                    ->count();
                            @endphp
                            @if($appointmentsCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $appointmentsCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Favorites Icon -->
                        <a href="{{ route('favorites.index') }}" class="relative hover:text-purple-600 dark:hover:text-purple-400 transition" title="{{ __('messages.favorites') }}">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            @php
                                $favoritesCount = Auth::user()->favorites()->count();
                            @endphp
                            @if($favoritesCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $favoritesCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Shopping Cart Icon -->
                        <a href="{{ route('cart.index') }}" class="relative hover:text-purple-600 dark:hover:text-purple-400 transition" title="Keranjang">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @php
                                $cartCount = Auth::user()->carts()->count();
                            @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Messages Icon -->
                        <a href="{{ route('chat.index') }}" class="relative hover:text-purple-600 dark:hover:text-purple-400 transition" title="Messages">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            @php
                                $unreadCount = App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Language Switcher -->
                        <x-language-switcher />

                        <!-- Dark Mode Toggle -->
                        <x-dark-mode-toggle />

                        <!-- User Greeting & Dropdown -->
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Hi, {{ Auth::user()->name }}</span>
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @else
                        <!-- Language Switcher for guests -->
                        <x-language-switcher />

                        <!-- Dark Mode Toggle for guests -->
                        <x-dark-mode-toggle />

                        <div class="space-x-4 ml-4">
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">{{ __('messages.login') }}</a>
                            <a href="{{ route('register') }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">{{ __('messages.register') }}</a>
                        </div>
                    @endauth
                </div>
                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    Home
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('professionals.index')" :active="request()->routeIs('professionals.*')">
                    Professionals
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                    Artikel
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('about')" :active="request()->routeIs('about')">
                    Tentang Kami
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                    Kontak
                </x-responsive-nav-link>
                @auth
                    @if(Auth::user()->role === 'professional')
                        <x-responsive-nav-link :href="route('professional.schedules.index')" :active="request()->routeIs('professional.schedules.*')">
                            Jadwal Saya
                        </x-responsive-nav-link>
                    @endif
                @endauth
            </div>

            @auth
                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">Hi, {{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">

                        <!-- Appointments with badge -->
                        @php
                            $appointmentsCount = Auth::user()->appointments()
                                ->where('status', 'confirmed')
                                ->whereHas('schedule', function($q) {
                                    $q->where('date', '>=', now()->toDateString());
                                })
                                ->count();
                        @endphp
                        <x-responsive-nav-link :href="route('appointments.index')">
                            <div class="flex items-center justify-between">
                                <span>Janji Temu</span>
                                @if($appointmentsCount > 0)
                                    <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $appointmentsCount }}
                                    </span>
                                @endif
                            </div>
                        </x-responsive-nav-link>

                        <!-- Favorites with badge -->
                        @php
                            $favoritesCount = Auth::user()->favorites()->count();
                        @endphp
                        <x-responsive-nav-link :href="route('favorites.index')">
                            <div class="flex items-center justify-between">
                                <span>{{ __('messages.favorites') }}</span>
                                @if($favoritesCount > 0)
                                    <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $favoritesCount }}
                                    </span>
                                @endif
                            </div>
                        </x-responsive-nav-link>

                        <!-- Cart with badge -->
                        @php
                            $cartCount = Auth::user()->carts()->count();
                        @endphp
                        <x-responsive-nav-link :href="route('cart.index')">
                            <div class="flex items-center justify-between">
                                <span>Keranjang</span>
                                @if($cartCount > 0)
                                    <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </div>
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-responsive-nav-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            @else
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4 space-y-2">
                        <a href="{{ route('login') }}" class="block text-gray-700 hover:text-gray-900">Login</a>
                        <a href="{{ route('register') }}" class="block text-gray-700 hover:text-gray-900">Register</a>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg pt-24 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    {!! __('messages.hero_heading') !!}
                </h1>
                <p class="text-xl text-purple-100 mb-8 max-w-3xl mx-auto">
                    {{ __('messages.hero_description') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                        {{ __('messages.start_now') }}
                    </a>
                    <a href="{{ route('professionals.index') }}" class="bg-purple-700 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-purple-800 transition">
                        {{ __('messages.view_professionals') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white dark:bg-gray-800 py-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">100+</div>
                    <div class="text-gray-600 dark:text-gray-400">{{ __('messages.stats_professionals') }}</div>
                </div>
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">10,000+</div>
                    <div class="text-gray-600 dark:text-gray-400">{{ __('messages.stats_consultations') }}</div>
                </div>
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">4.9/5</div>
                    <div class="text-gray-600 dark:text-gray-400">{{ __('messages.stats_rating') }}</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('messages.why_choose_title') }}</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ __('messages.why_choose_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.feature_verified_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.feature_verified_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.feature_privacy_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.feature_privacy_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.feature_flexible_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.feature_flexible_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.feature_video_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.feature_video_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.feature_affordable_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.feature_affordable_desc') }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.feature_easy_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.feature_easy_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-white dark:bg-gray-800 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ __('messages.how_it_works_title') }}</h2>
                <p class="text-xl text-gray-600 dark:text-gray-400">{{ __('messages.how_it_works_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.step1_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.step1_desc') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.step2_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.step2_desc') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.step3_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.step3_desc') }}</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">4</div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.step4_title') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.step4_desc') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recommended Professionals -->
    <x-recommended-professionals :recommendations="$recommendations" />

    <!-- CTA Section -->
    <section class="gradient-bg py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">{{ __('messages.cta_title') }}</h2>
            <p class="text-xl text-purple-100 mb-8">{{ __('messages.cta_subtitle') }}</p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                {{ __('messages.cta_button') }}
            </a>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-16 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-newsletter-form />
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-black text-gray-300 py-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white text-xl font-bold mb-4">Teman Bicara</h3>
                    <p class="text-gray-400">{{ __('messages.footer_tagline') }}</p>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('messages.footer_navigation') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="/" class="hover:text-white transition">{{ __('messages.home') }}</a></li>
                        <li><a href="{{ route('professionals.index') }}" class="hover:text-white transition">{{ __('messages.professionals') }}</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-white transition">{{ __('messages.articles') }}</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">{{ __('messages.about_us') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('messages.footer_services') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('professionals.index') }}" class="hover:text-white transition">{{ __('messages.psychiatrist') }}</a></li>
                        <li><a href="{{ route('professionals.index') }}" class="hover:text-white transition">{{ __('messages.psychologist') }}</a></li>
                        <li><a href="{{ route('professionals.index') }}" class="hover:text-white transition">{{ __('messages.conversationalist') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('messages.footer_contact_title') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">{{ __('messages.footer_contact_us') }}</a></li>
                        <li><a href="#" class="hover:text-white transition">{{ __('messages.footer_email') }}</a></li>
                        <li><a href="#" class="hover:text-white transition">{{ __('messages.footer_phone') }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p>&copy; {{ date('Y') }} Teman Bicara. {{ __('messages.footer_copyright') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
