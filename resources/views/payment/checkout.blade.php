<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Checkout
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">Ringkasan Pesanan</h3>

                    <div class="space-y-3 mb-6">
                        @foreach($carts as $cart)
                            <div class="border rounded p-3">
                                <p class="font-semibold">{{ $cart->professional->user->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $cart->schedule->date->format('d F Y') }} | {{ $cart->schedule->start_time }}
                                </p>
                                <p class="text-sm text-gray-600">Durasi: {{ $cart->duration }} menit</p>
                                <p class="font-semibold text-right">Rp {{ number_format($cart->price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-semibold">Total Pembayaran:</span>
                            <span class="text-2xl font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('payment.process') }}" method="POST">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran:</label>
                            <select name="payment_method" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="credit_card">Kartu Kredit</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="e-wallet">E-Wallet</option>
                            </select>
                            @error('payment_method')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-6">
                            <p class="text-sm text-yellow-800">
                                <strong>Catatan:</strong> Ini adalah demo. Pembayaran akan otomatis berhasil dan Anda akan menerima email konfirmasi.
                            </p>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-3 rounded-md hover:bg-indigo-700 font-semibold">
                            Bayar Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
