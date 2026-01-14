<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Berikan Review
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Appointment Details -->
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="font-semibold text-lg mb-2 text-gray-800 dark:text-gray-200">Detail Konsultasi</h3>
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <p><strong>Professional:</strong> {{ $appointment->professional->user->name }}</p>
                            <p><strong>Tanggal:</strong> {{ $appointment->appointment_date->format('d F Y') }}</p>
                            <p><strong>Waktu:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                            <p><strong>Durasi:</strong> {{ $appointment->duration }} menit</p>
                        </div>
                    </div>

                    <!-- Review Form -->
                    <form action="{{ route('reviews.store', $appointment) }}" method="POST">
                        @csrf

                        <!-- Rating -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rating <span class="text-red-600">*</span>
                            </label>
                            <div class="flex items-center space-x-2" x-data="{ rating: {{ old('rating', 0) }} }">
                                <template x-for="star in 5" :key="star">
                                    <button type="button"
                                            @click="rating = star"
                                            class="text-3xl focus:outline-none transition-colors"
                                            :class="star <= rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'">
                                        ★
                                    </button>
                                </template>
                                <input type="hidden" name="rating" :value="rating">
                            </div>
                            @error('rating')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div class="mb-6">
                            <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Komentar (opsional)
                            </label>
                            <textarea name="comment"
                                      id="comment"
                                      rows="5"
                                      maxlength="1000"
                                      class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Bagikan pengalaman Anda dengan professional ini...">{{ old('comment') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maksimal 1000 karakter</p>
                            @error('comment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('appointments.index') }}"
                               class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Kirim Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
