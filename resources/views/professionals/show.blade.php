<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Professional
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <div class="text-center">
                                <div class="w-32 h-32 bg-gray-300 rounded-full mx-auto mb-4"></div>
                                <h3 class="font-semibold text-xl">{{ $professional->user->name }}</h3>
                                <p class="text-gray-600">{{ ucfirst($professional->type) }}</p>
                                @if($professional->license_number)
                                    <p class="text-sm text-gray-500 mt-2">Lisensi: {{ $professional->license_number }}</p>
                                @endif
                            </div>

                            <div class="mt-6 space-y-3">
                                <div>
                                    <p class="font-semibold">Spesialisasi:</p>
                                    <p class="text-gray-700">{{ $professional->specialization }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Pengalaman:</p>
                                    <p class="text-gray-700">{{ $professional->experience_years }} tahun</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Harga:</p>
                                    <p class="text-gray-700">30 menit: Rp {{ number_format($professional->rate_30min, 0, ',', '.') }}</p>
                                    <p class="text-gray-700">60 menit: Rp {{ number_format($professional->rate_60min, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <p class="font-semibold mb-2">Bio:</p>
                                <p class="text-gray-700 text-sm">{{ $professional->bio }}</p>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="font-semibold text-lg mb-4">Jadwal Tersedia</h4>

                            @if($availableSchedules->isEmpty())
                                <p class="text-gray-600">Belum ada jadwal tersedia.</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($availableSchedules as $schedule)
                                        <div class="border rounded-lg p-4">
                                            <p class="font-semibold">{{ $schedule->date->format('d F Y') }}</p>
                                            <p class="text-gray-600">{{ $schedule->start_time }} - {{ $schedule->end_time }}</p>

                                            @auth
                                                <form action="{{ route('cart.store') }}" method="POST" class="mt-3">
                                                    @csrf
                                                    <input type="hidden" name="professional_id" value="{{ $professional->id }}">
                                                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Durasi:</label>
                                                        <select name="duration" required
                                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                            <option value="30">30 menit - Rp {{ number_format($professional->rate_30min, 0, ',', '.') }}</option>
                                                            <option value="60">60 menit - Rp {{ number_format($professional->rate_60min, 0, ',', '.') }}</option>
                                                        </select>
                                                    </div>

                                                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                                        Tambah ke Keranjang
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('login') }}" class="block w-full text-center bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 mt-3">
                                                    Login untuk Booking
                                                </a>
                                            @endauth
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
