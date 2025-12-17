<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Jadwal Saya
            </h2>
            <a href="{{ route('professional.schedules.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                + Tambah Jadwal
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">{{ $professional->user->name }}</h3>
                    <p class="text-gray-600">{{ ucfirst($professional->type) }}</p>
                </div>

                @if($schedules->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($schedules as $schedule)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->diffInMinutes(\Carbon\Carbon::parse($schedule->end_time)) }} menit
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($schedule->is_available)
                                                @if($schedule->appointments->count() > 0)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">
                                                        Terbooking
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                                                        Tersedia
                                                    </span>
                                                @endif
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                                                    Tidak Tersedia
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($schedule->appointments->count() == 0)
                                                <form action="{{ route('professional.schedules.destroy', $schedule) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">Tidak dapat dihapus</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6">
                        {{ $schedules->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Belum ada jadwal</h3>
                        <p class="mt-1 text-gray-500">Mulai dengan menambahkan jadwal tersedia Anda</p>
                        <a href="{{ route('professional.schedules.create') }}" class="mt-4 inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            Tambah Jadwal Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
