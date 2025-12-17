<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hubungi Kami
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Contact Form -->
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <h2 class="text-2xl font-bold mb-2">Kirim Pesan</h2>
                    <p class="text-gray-600 mb-6">Isi formulir di bawah ini dan tim kami akan menghubungi Anda sesegera mungkin.</p>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                            <input type="text" id="name" name="name" required
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="Nama Anda">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="email@example.com">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 font-medium mb-2">Nomor Telepon</label>
                            <input type="text" id="phone" name="phone"
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="+62">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="block text-gray-700 font-medium mb-2">Subjek</label>
                            <select id="subject" name="subject" required
                                    class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                                <option value="">Pilih Subjek</option>
                                <option value="general">Pertanyaan Umum</option>
                                <option value="booking">Bantuan Booking</option>
                                <option value="technical">Masalah Teknis</option>
                                <option value="professional">Menjadi Profesional</option>
                                <option value="partnership">Kerjasama</option>
                                <option value="other">Lainnya</option>
                            </select>
                            @error('subject')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="message" class="block text-gray-700 font-medium mb-2">Pesan</label>
                            <textarea id="message" name="message" rows="5" required
                                      class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500"
                                      placeholder="Tulis pesan Anda di sini..."></textarea>
                            @error('message')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                            Kirim Pesan
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-6">
                    <!-- Contact Details -->
                    <div class="bg-white rounded-lg shadow-sm p-8">
                        <h2 class="text-2xl font-bold mb-6">Informasi Kontak</h2>

                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Email</h3>
                                    <p class="text-gray-600">info@temanbicara.com</p>
                                    <p class="text-gray-600">support@temanbicara.com</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Telepon</h3>
                                    <p class="text-gray-600">+62 21 1234 5678</p>
                                    <p class="text-gray-600">+62 812 3456 7890</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Alamat</h3>
                                    <p class="text-gray-600">Jl. Sudirman No. 123<br>Jakarta Pusat 10110<br>Indonesia</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1">Jam Operasional</h3>
                                    <p class="text-gray-600">Senin - Jumat: 08:00 - 20:00</p>
                                    <p class="text-gray-600">Sabtu - Minggu: 09:00 - 17:00</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Quick Links -->
                    <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg shadow-sm p-8 text-white">
                        <h2 class="text-2xl font-bold mb-4">Pertanyaan Umum?</h2>
                        <p class="mb-6 text-purple-100">Mungkin jawaban yang Anda cari sudah ada di halaman FAQ kami.</p>
                        <a href="{{ route('articles.index') }}" class="inline-block bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Lihat Artikel & FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
