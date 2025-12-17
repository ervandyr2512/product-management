<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Jadwal Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-8">
                <form action="{{ route('professional.schedules.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="date" class="block text-gray-700 font-medium mb-2">Tanggal</label>
                        <input type="date" id="date" name="date" value="{{ old('date') }}" required
                               min="{{ date('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        @error('date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="start_time" class="block text-gray-700 font-medium mb-2">Waktu Mulai</label>
                            <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            @error('start_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-gray-700 font-medium mb-2">Waktu Selesai</label>
                            <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" required
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            @error('end_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Catatan</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Pastikan waktu yang Anda pilih sesuai dengan ketersediaan Anda</li>
                                        <li>Jadwal hanya dapat dihapus jika belum ada yang booking</li>
                                        <li>Format waktu adalah 24 jam (contoh: 14:00 untuk jam 2 siang)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('professional.schedules.index') }}"
                           class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
