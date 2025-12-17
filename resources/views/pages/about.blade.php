<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tentang Kami
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-12 text-white mb-8">
                <h1 class="text-4xl font-bold mb-4">Teman Bicara</h1>
                <p class="text-xl text-purple-100">Platform Konsultasi Kesehatan Mental Terpercaya di Indonesia</p>
            </div>

            <!-- Mission & Vision -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">Misi Kami</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Memberikan akses mudah dan terjangkau kepada layanan kesehatan mental profesional untuk semua orang di Indonesia. Kami percaya bahwa kesehatan mental adalah hak setiap individu dan harus dapat diakses dengan mudah.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-8">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4">Visi Kami</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Menjadi platform konsultasi kesehatan mental terdepan di Indonesia yang membantu jutaan orang menjalani kehidupan yang lebih sehat dan bahagia secara mental.
                    </p>
                </div>
            </div>

            <!-- Our Story -->
            <div class="bg-white rounded-lg shadow-sm p-8 mb-12">
                <h2 class="text-3xl font-bold mb-6">Cerita Kami</h2>
                <div class="space-y-4 text-gray-600 leading-relaxed">
                    <p>
                        Teman Bicara didirikan pada tahun 2023 dengan visi untuk menghadirkan layanan konsultasi kesehatan mental yang mudah diakses oleh masyarakat Indonesia. Kami menyadari bahwa stigma dan keterbatasan akses masih menjadi hambatan utama bagi banyak orang dalam mencari bantuan profesional untuk kesehatan mental mereka.
                    </p>
                    <p>
                        Melalui platform digital kami, pengguna dapat dengan mudah terhubung dengan psikiater, psikolog, dan conversationalist profesional yang telah terverifikasi. Kami menyediakan konsultasi video yang aman dan privat, memungkinkan setiap orang untuk mendapatkan dukungan yang mereka butuhkan dari kenyamanan rumah mereka sendiri.
                    </p>
                    <p>
                        Sejak diluncurkan, Teman Bicara telah membantu ribuan orang di seluruh Indonesia untuk mendapatkan akses ke layanan kesehatan mental profesional. Kami terus berkembang dan berkomitmen untuk terus meningkatkan kualitas layanan kami.
                    </p>
                </div>
            </div>

            <!-- Our Values -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold mb-8 text-center">Nilai-Nilai Kami</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Privasi & Keamanan</h3>
                        <p class="text-gray-600">Kami menjaga kerahasiaan setiap sesi konsultasi dengan enkripsi tingkat tinggi.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Kualitas Terjamin</h3>
                        <p class="text-gray-600">Semua profesional telah melalui proses verifikasi dan seleksi yang ketat.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Peduli & Empati</h3>
                        <p class="text-gray-600">Kami memahami perjalanan kesehatan mental setiap individu adalah unik.</p>
                    </div>
                </div>
            </div>

            <!-- Team Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-3xl font-bold mb-6 text-center">Tim Profesional Kami</h2>
                <p class="text-gray-600 text-center mb-8 max-w-3xl mx-auto">
                    Teman Bicara bekerja sama dengan lebih dari 100 profesional kesehatan mental yang tersebar di seluruh Indonesia, termasuk psikiater bersertifikat, psikolog klinis, dan conversationalist berpengalaman.
                </p>
                <div class="text-center">
                    <a href="{{ route('professionals.index') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Lihat Profesional Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
