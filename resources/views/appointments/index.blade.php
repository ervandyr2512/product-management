<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Janji Temu Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
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

                    @if($appointments->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-600 mb-4">Anda belum memiliki janji temu</p>
                            <a href="{{ route('professionals.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Cari Professional
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($appointments as $appointment)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h3 class="font-semibold text-lg">{{ $appointment->professional->user->name }}</h3>
                                                <span class="px-2 py-1 rounded text-xs font-semibold
                                                    @if($appointment->status == 'confirmed') bg-green-100 text-green-800
                                                    @elseif($appointment->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($appointment->status == 'completed') bg-blue-100 text-blue-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($appointment->status) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600">{{ ucfirst($appointment->professional->type) }}</p>
                                            <p class="text-sm text-gray-600 mt-2">
                                                <strong>Tanggal:</strong> {{ $appointment->appointment_date->format('d F Y') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <strong>Waktu:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <strong>Durasi:</strong> {{ $appointment->duration }} menit
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <strong>Harga:</strong> Rp {{ number_format($appointment->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="text-right space-y-2">
                                            <a href="{{ route('appointments.show', $appointment) }}"
                                               class="block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                                                Detail
                                            </a>
                                            @if($appointment->status == 'confirmed' && $appointment->video_chat_link)
                                                <a href="{{ $appointment->video_chat_link }}"
                                                   target="_blank"
                                                   class="block bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                                    Join Video Chat
                                                </a>
                                            @endif
                                            @if(in_array($appointment->status, ['pending', 'confirmed']))
                                                <form action="{{ route('appointments.cancel', $appointment) }}" method="POST"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')">
                                                    @csrf
                                                    <button type="submit" class="block w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm">
                                                        Batalkan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $appointments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
