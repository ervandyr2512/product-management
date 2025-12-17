<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Janji Temu
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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

                    <div class="border-b pb-4 mb-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-semibold">{{ $appointment->professional->user->name }}</h3>
                            <span class="px-3 py-1 rounded text-sm font-semibold
                                @if($appointment->status == 'confirmed') bg-green-100 text-green-800
                                @elseif($appointment->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($appointment->status == 'completed') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                        <p class="text-gray-600">{{ ucfirst($appointment->professional->type) }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-700">Tanggal & Waktu</h4>
                                <p class="text-gray-600">{{ $appointment->appointment_date->format('d F Y') }}</p>
                                <p class="text-gray-600">{{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700">Durasi</h4>
                                <p class="text-gray-600">{{ $appointment->duration }} menit</p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700">Harga</h4>
                                <p class="text-gray-600">Rp {{ number_format($appointment->price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @if($appointment->payment)
                                <div>
                                    <h4 class="font-semibold text-gray-700">Status Pembayaran</h4>
                                    <p class="text-gray-600">{{ ucfirst($appointment->payment->status) }}</p>
                                    @if($appointment->payment->paid_at)
                                        <p class="text-sm text-gray-500">Dibayar: {{ $appointment->payment->paid_at->format('d F Y H:i') }}</p>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-700">Metode Pembayaran</h4>
                                    <p class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $appointment->payment->payment_method)) }}</p>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-700">ID Transaksi</h4>
                                    <p class="text-gray-600 text-sm">{{ $appointment->payment->payment_gateway_id }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($appointment->video_chat_link && $appointment->status == 'confirmed')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-blue-800 mb-2">Link Video Chat</h4>
                            <p class="text-sm text-blue-700 mb-3">Gunakan link di bawah ini untuk bergabung dengan sesi video chat:</p>
                            <a href="{{ $appointment->video_chat_link }}"
                               target="_blank"
                               class="inline-block bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                Join Video Chat
                            </a>
                        </div>
                    @endif

                    @if($appointment->notes)
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 mb-2">Catatan</h4>
                            <p class="text-gray-600 bg-gray-50 p-4 rounded">{{ $appointment->notes }}</p>
                        </div>
                    @endif

                    <div class="flex gap-3">
                        <a href="{{ route('appointments.index') }}"
                           class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700">
                            Kembali ke Daftar
                        </a>
                        @if(in_array($appointment->status, ['pending', 'confirmed']))
                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700">
                                    Batalkan Appointment
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
