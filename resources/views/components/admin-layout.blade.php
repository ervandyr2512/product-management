<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin Panel - {{ config('app.name', 'Laravel') }}</title>

        <!-- Dark Mode Script (prevent flicker) -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class'
            }
        </script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
            <!-- Sidebar -->
            <div
                x-show="sidebarOpen"
                @click="sidebarOpen = false"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-75 z-20 lg:hidden"
            ></div>

            <aside
                x-show="sidebarOpen"
                x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                @click.away="sidebarOpen = false"
                class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-800 shadow-lg lg:static lg:translate-x-0 lg:z-0"
            >
                <div class="flex flex-col h-full">
                    <!-- Sidebar Header -->
                    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-lg">TB</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">Admin Panel</span>
                        </a>
                        <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center space-x-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span class="font-medium">Dashboard</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center space-x-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <span class="font-medium">User Management</span>
                        </a>

                        <a href="{{ route('admin.professionals.index') }}"
                           class="flex items-center space-x-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.professionals.*') ? 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-medium">Professionals</span>
                        </a>

                        <a href="{{ route('admin.settings.index') }}"
                           class="flex items-center space-x-3 px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.settings.*') ? 'bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Settings</span>
                        </a>

                        <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

                        <a href="{{ route('home') }}"
                           class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            <span class="font-medium">View Site</span>
                        </a>
                    </nav>

                    <!-- User Section -->
                    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if(Auth::user()->profile_photo)
                                    <img class="w-10 h-10 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                        <span class="text-purple-600 dark:text-purple-200 font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Navigation Bar -->
                <header class="bg-white dark:bg-gray-800 shadow-sm z-10">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center space-x-4">
                            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>

                            <!-- Website Navigation Links -->
                            <nav class="hidden md:flex items-center space-x-6">
                                <a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                    Home
                                </a>
                                <a href="{{ route('professionals.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                    Professionals
                                </a>
                                <a href="{{ route('articles.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                    Articles
                                </a>
                                <a href="{{ route('about') }}" class="text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                    About
                                </a>
                                <a href="{{ route('contact') }}" class="text-gray-600 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition">
                                    Contact
                                </a>
                            </nav>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Dark Mode Toggle -->
                            <button
                                @click="
                                    if (document.documentElement.classList.contains('dark')) {
                                        document.documentElement.classList.remove('dark');
                                        localStorage.setItem('theme', 'light');
                                    } else {
                                        document.documentElement.classList.add('dark');
                                        localStorage.setItem('theme', 'dark');
                                    }
                                "
                                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            >
                                <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </header>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                        <div class="bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 p-4 rounded">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-green-700 dark:text-green-200">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                        <div class="bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 p-4 rounded">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Scripts -->
        @stack('scripts')
    </body>
</html>
