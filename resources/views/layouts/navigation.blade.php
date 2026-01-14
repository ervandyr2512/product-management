<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 transition-colors duration-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                        Teman Bicara
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('messages.home') }}
                    </x-nav-link>
                    <x-nav-link :href="route('professionals.index')" :active="request()->routeIs('professionals.*')">
                        {{ __('messages.professionals') }}
                    </x-nav-link>
                    <x-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                        {{ __('messages.articles') }}
                    </x-nav-link>
                    <x-nav-link :href="route('about')" :active="request()->routeIs('about')">
                        {{ __('messages.about_us') }}
                    </x-nav-link>
                    <x-nav-link :href="route('contact')" :active="request()->routeIs('contact')">
                        {{ __('messages.contact') }}
                    </x-nav-link>
                    @auth
                        @if(Auth::user()->role === 'professional')
                            <x-nav-link :href="route('professional.schedules.index')" :active="request()->routeIs('professional.schedules.*')">
                                {{ __('messages.my_schedule') }}
                            </x-nav-link>
                        @endif
                        @if(Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                                Admin Panel
                            </x-nav-link>
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
