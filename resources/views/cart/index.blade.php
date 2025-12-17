<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Keranjang Belanja
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

                    @if($carts->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-600 mb-4">Keranjang Anda kosong</p>
                            <a href="{{ route('professionals.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Browse Professionals
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($carts as $cart)
                                <div class="border rounded-lg p-4 flex justify-between items-center">
                                    <div class="flex-1">
                                        <h3 class="font-semibold">{{ $cart->professional->user->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ ucfirst($cart->professional->type) }}</p>
                                        <p class="text-sm text-gray-600">
                                            {{ $cart->schedule->date->format('d F Y') }} | {{ $cart->schedule->start_time }} - {{ $cart->schedule->end_time }}
                                        </p>
                                        <p class="text-sm text-gray-600">Durasi: {{ $cart->duration }} menit</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-lg">Rp {{ number_format($cart->price, 0, ',', '.') }}</p>
                                        <form action="{{ route('cart.destroy', $cart) }}" method="POST" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 border-t pt-6">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xl font-semibold">Total:</span>
                                <span class="text-2xl font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <a href="{{ route('payment.checkout') }}" class="block w-full text-center bg-indigo-600 text-white px-4 py-3 rounded-md hover:bg-indigo-700 font-semibold">
                                Lanjut ke Pembayaran
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
