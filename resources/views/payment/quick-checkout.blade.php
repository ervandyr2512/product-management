<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                        Konfirmasi Booking
                    </h3>

                    <!-- Booking Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-16 h-16 bg-gray-300 dark:bg-gray-600 rounded-full flex-shrink-0"></div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white">
                                    {{ $professional->user->name }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ ucfirst($professional->type) }} • {{ $professional->specialization }}
                                </p>
                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($schedule->date)->format('d F Y') }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-700 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                        {{ $request->duration }} menit
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form method="POST" action="{{ route('payment.quick-process', $schedule) }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="duration" value="{{ $request->duration }}">

                        <!-- Payment Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                {{ __('messages.payment_method') }}
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="mr-3" required checked>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Bank Transfer</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Transfer ke rekening bank</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <input type="radio" name="payment_method" value="e-wallet" class="mr-3" required>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">E-Wallet</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">GoPay, OVO, Dana, dll</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-4 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <input type="radio" name="payment_method" value="credit_card" class="mr-3" required>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Credit Card</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Visa, Mastercard, dll</div>
                                    </div>
                                </label>
                            </div>
                            @error('payment_method')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order Summary -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                {{ __('messages.order_summary') }}
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                    <span>Konsultasi {{ $request->duration }} menit</span>
                                    <span>Rp {{ number_format($price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                    <span>{{ __('messages.admin_fee') }}</span>
                                    <span>Rp 0</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                                    <span>{{ __('messages.grand_total') }}</span>
                                    <span>Rp {{ number_format($price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <a href="{{ route('professionals.show', $professional) }}"
                               class="flex-1 text-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition">
                                {{ __('messages.back') }}
                            </a>
                            <button type="submit"
                                    class="flex-1 bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 font-medium transition">
                                {{ __('messages.process_payment') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
