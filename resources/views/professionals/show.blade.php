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
                                <h3 class="font-semibold text-xl dark:text-white">{{ $professional->user->name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ ucfirst($professional->type) }}</p>
                                @if($professional->license_number)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Lisensi: {{ $professional->license_number }}</p>
                                @endif

                                <!-- Rating Display -->
                                @if($totalReviews > 0)
                                    <div class="mt-3 flex items-center justify-center space-x-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="text-xl {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">★</span>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ number_format($averageRating, 1) }} ({{ $totalReviews }} {{ $totalReviews == 1 ? 'review' : 'reviews' }})
                                        </span>
                                    </div>
                                @endif

                                <!-- Favorite Button -->
                                <div class="mt-4 flex justify-center">
                                    <x-favorite-button
                                        :professional-id="$professional->id"
                                        :is-favorited="$isFavorited ?? false"
                                    />
                                </div>
                            </div>

                            <!-- Share Buttons -->
                            <div class="mt-6 flex justify-center">
                                <x-share-buttons
                                    :url="route('professionals.show', $professional)"
                                    :title="$professional->user->name . ' - ' . ucfirst($professional->type)"
                                    :description="$professional->specialization . ' | ' . $professional->experience_years . ' tahun pengalaman'"
                                    type="horizontal"
                                />
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
                            <h4 class="font-semibold text-lg mb-4">{{ __('messages.available_schedule') }}</h4>

                            @if($availableSchedules->isEmpty())
                                <p class="text-gray-600">{{ __('messages.no_schedule_desc') }}</p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($availableSchedules as $schedule)
                                        <div class="border rounded-lg p-4">
                                            <p class="font-semibold">{{ $schedule->date->format('d F Y') }}</p>
                                            <p class="text-gray-600">{{ $schedule->start_time }} - {{ $schedule->end_time }}</p>

                                            @auth
                                                <div class="mt-3" x-data="{ duration: '30' }">
                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi:</label>
                                                        <select x-model="duration" required
                                                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50">
                                                            <option value="30">30 menit - Rp {{ number_format($professional->rate_30min, 0, ',', '.') }}</option>
                                                            <option value="60">60 menit - Rp {{ number_format($professional->rate_60min, 0, ',', '.') }}</option>
                                                        </select>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-2">
                                                        <!-- Quick Book Button -->
                                                        <a :href="`{{ route('payment.quick-checkout', $schedule) }}?duration=${duration}`"
                                                           class="text-center bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition font-medium">
                                                            {{ __('messages.book_directly') }}
                                                        </a>

                                                        <!-- Add to Cart Button -->
                                                        <form action="{{ route('cart.store') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="professional_id" value="{{ $professional->id }}">
                                                            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                            <input type="hidden" name="duration" x-model="duration">
                                                            <button type="submit" class="w-full bg-white dark:bg-gray-700 border border-purple-600 dark:border-purple-400 text-purple-600 dark:text-purple-400 px-4 py-2 rounded-md hover:bg-purple-50 dark:hover:bg-gray-600 transition font-medium">
                                                                {{ __('messages.add_to_cart') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
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

                    <!-- Reviews Section -->
                    @if($totalReviews > 0)
                        <div class="mt-8 border-t pt-8">
                            <h4 class="font-semibold text-lg mb-6 dark:text-gray-200">Reviews ({{ $totalReviews }})</h4>
                            <div class="space-y-4">
                                @foreach($reviews as $review)
                                    <div class="border rounded-lg p-4 dark:border-gray-700">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-3 mb-2">
                                                    <h5 class="font-semibold dark:text-gray-200">{{ $review->user->name }}</h5>
                                                    <div class="flex items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <span class="text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">★</span>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if($review->comment)
                                                    <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $review->comment }}</p>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
