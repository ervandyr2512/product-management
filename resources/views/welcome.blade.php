<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Platform Konsultasi Kesehatan Mental</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
<body class="antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <span class="text-2xl font-bold gradient-text">Teman Bicara</span>
                    </a>
                    <div class="hidden md:flex md:ml-10 md:space-x-8">
                        <a href="/" class="text-gray-900 hover:text-purple-600 px-3 py-2 text-sm font-medium">Home</a>
                        <a href="{{ route('professionals.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Professionals</a>
                        <a href="{{ route('articles.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Artikel</a>
                        <a href="{{ route('about') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Tentang Kami</a>
                        <a href="{{ route('contact') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Kontak</a>
                    </div>
                </div>
                <div class="hidden md:flex md:items-center md:space-x-4">
                    @auth
                        <!-- Appointments Icon -->
                        <a href="{{ route('appointments.index') }}" class="relative hover:text-purple-600 transition" title="Janji Temu">
                            <svg class="w-6 h-6 text-gray-600 hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        <!-- Shopping Cart Icon -->
                        <a href="{{ route('cart.index') }}" class="relative hover:text-purple-600 transition" title="Keranjang">
                            <svg class="w-6 h-6 text-gray-600 hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        @if(Auth::user()->role === 'professional')
                            <a href="{{ route('professional.schedules.index') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Jadwal Saya</a>
                        @endif

                        <!-- User Greeting -->
                        <span class="text-sm text-gray-700">Hi, {{ Auth::user()->name }}</span>

                        <!-- Profile Dropdown Button -->
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="dropdownOpen"
                                 @click.away="dropdownOpen = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                 style="display: none;">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600 px-3 py-2 text-sm font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700">Daftar</a>
                    @endauth
                </div>
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="text-gray-700 hover:text-purple-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': !open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path :class="{'hidden': !open, 'inline-flex': open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="open" @click.away="open = false" class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t">
                <a href="/" class="block px-3 py-2 text-gray-900 hover:bg-purple-50">Home</a>
                <a href="{{ route('professionals.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-purple-50">Professionals</a>
                <a href="{{ route('articles.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-purple-50">Artikel</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 text-gray-700 hover:bg-purple-50">Tentang Kami</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 text-gray-700 hover:bg-purple-50">Kontak</a>
                @auth
                    @if(Auth::user()->role === 'professional')
                        <a href="{{ route('professional.schedules.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-purple-50">Jadwal Saya</a>
                    @endif

                    <div class="border-t border-gray-200 pt-2 mt-2">
                        <div class="px-3 py-2">
                            <div class="font-medium text-base text-gray-800">Hi, {{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>

                    <!-- Appointments with badge -->
                    @php
                        $appointmentsCount = Auth::user()->appointments()
                            ->where('status', 'confirmed')
                            ->whereHas('schedule', function($q) {
                                $q->where('date', '>=', now()->toDateString());
                            })
                            ->count();
                    @endphp
                    <a href="{{ route('appointments.index') }}" class="flex items-center justify-between px-3 py-2 text-gray-700 hover:bg-purple-50">
                        <span>Janji Temu</span>
                        @if($appointmentsCount > 0)
                            <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $appointmentsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Cart with badge -->
                    @php
                        $cartCount = Auth::user()->carts()->count();
                    @endphp
                    <a href="{{ route('cart.index') }}" class="flex items-center justify-between px-3 py-2 text-gray-700 hover:bg-purple-50">
                        <span>Keranjang</span>
                        @if($cartCount > 0)
                            <span class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-gray-700 hover:bg-purple-50">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-gray-700 hover:bg-purple-50">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-700 hover:bg-purple-50">Login</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-purple-600 font-medium hover:bg-purple-50">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg pt-24 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Kesehatan Mental<br>Adalah Prioritas
                </h1>
                <p class="text-xl text-purple-100 mb-8 max-w-3xl mx-auto">
                    Terhubung dengan psikiater, psikolog, dan conversationalist profesional untuk konsultasi kesehatan mental Anda kapan saja, di mana saja.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                        Mulai Sekarang
                    </a>
                    <a href="{{ route('professionals.index') }}" class="bg-purple-700 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-purple-800 transition">
                        Lihat Profesional
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">100+</div>
                    <div class="text-gray-600">Profesional Berpengalaman</div>
                </div>
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">10,000+</div>
                    <div class="text-gray-600">Konsultasi Sukses</div>
                </div>
                <div>
                    <div class="text-4xl font-bold gradient-text mb-2">4.9/5</div>
                    <div class="text-gray-600">Rating Kepuasan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Mengapa Memilih Teman Bicara?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Platform terpercaya untuk kesehatan mental Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Profesional Terverifikasi</h3>
                    <p class="text-gray-600">Semua psikiater, psikolog, dan conversationalist telah melalui verifikasi ketat</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Privasi Terjamin</h3>
                    <p class="text-gray-600">Data dan percakapan Anda dilindungi dengan enkripsi tingkat tinggi</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Jadwal Fleksibel</h3>
                    <p class="text-gray-600">Booking konsultasi sesuai waktu yang Anda inginkan, 24/7</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Video Consultation</h3>
                    <p class="text-gray-600">Konsultasi tatap muka virtual dengan kualitas HD</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Harga Terjangkau</h3>
                    <p class="text-gray-600">Berbagai pilihan paket konsultasi dengan harga yang kompetitif</p>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Mudah & Cepat</h3>
                    <p class="text-gray-600">Proses booking yang simpel, konfirmasi instan via email & WhatsApp</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Cara Kerja</h2>
                <p class="text-xl text-gray-600">Hanya 4 langkah mudah untuk memulai konsultasi</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-lg font-semibold mb-2">Daftar/Login</h3>
                    <p class="text-gray-600">Buat akun atau login ke platform Teman Bicara</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-lg font-semibold mb-2">Pilih Profesional</h3>
                    <p class="text-gray-600">Browse dan pilih profesional sesuai kebutuhan Anda</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-lg font-semibold mb-2">Booking & Bayar</h3>
                    <p class="text-gray-600">Pilih jadwal yang tersedia dan lakukan pembayaran</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">4</div>
                    <h3 class="text-lg font-semibold mb-2">Mulai Konsultasi</h3>
                    <p class="text-gray-600">Join video chat sesuai jadwal yang telah ditentukan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="gradient-bg py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Siap Memulai Perjalanan Kesehatan Mental Anda?</h2>
            <p class="text-xl text-purple-100 mb-8">Bergabunglah dengan ribuan orang yang telah merasakan manfaatnya</p>
            <a href="{{ route('register') }}" class="inline-block bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                Daftar Gratis Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white text-xl font-bold mb-4">Teman Bicara</h3>
                    <p class="text-gray-400">Platform konsultasi kesehatan mental terpercaya di Indonesia.</p>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">Navigasi</h4>
                    <ul class="space-y-2">
                        <li><a href="/" class="hover:text-white transition">Home</a></li>
                        <li><a href="{{ route('professionals.index') }}" class="hover:text-white transition">Professionals</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-white transition">Artikel</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">Tentang Kami</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">Layanan</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('professionals.index') }}" class="hover:text-white transition">Psikiater</a></li>
                        <li><a href="{{ route('professionals.index') }}" class="hover:text-white transition">Psikolog</a></li>
                        <li><a href="{{ route('professionals.index') }}" class="hover:text-white transition">Conversationalist</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Hubungi Kami</a></li>
                        <li><a href="#" class="hover:text-white transition">Email: info@temanbicara.com</a></li>
                        <li><a href="#" class="hover:text-white transition">Telp: +62 21 1234 5678</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p>&copy; {{ date('Y') }} Teman Bicara. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
