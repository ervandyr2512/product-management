@props(['recommendations'])

@if($recommendations && $recommendations->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('messages.recommended_for_you') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    {{ __('messages.recommended_description') }}
                </p>
            </div>
            <a href="{{ route('professionals.index') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                {{ __('messages.view_all') }} →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recommendations as $professional)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                @if($professional->profile_photo)
                                    <img src="{{ asset('storage/' . $professional->profile_photo) }}"
                                         alt="{{ $professional->user->name }}"
                                         class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                        {{ $professional->user->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ ucfirst($professional->type) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($professional->specialization)
                            <div class="mb-3">
                                <span class="inline-block bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs px-2 py-1 rounded">
                                    {{ $professional->specialization }}
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center mb-3">
                            <div class="flex items-center">
                                @php
                                    $avgRating = $professional->reviews_avg_rating ?? 0;
                                    $reviewCount = $professional->reviews_count ?? 0;
                                @endphp
                                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="ml-1 text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($avgRating, 1) }} ({{ $reviewCount }} {{ __('messages.reviews') }})
                                </span>
                            </div>
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                            {{ Str::limit($professional->bio, 100) }}
                        </p>

                        <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
                            <div>
                                <span class="font-semibold text-purple-600 dark:text-purple-400">
                                    Rp {{ number_format($professional->rate_30min, 0, ',', '.') }}
                                </span>
                                <span class="text-xs">/30 {{ __('messages.minutes') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-purple-600 dark:text-purple-400">
                                    Rp {{ number_format($professional->rate_60min, 0, ',', '.') }}
                                </span>
                                <span class="text-xs">/60 {{ __('messages.minutes') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('professionals.show', $professional) }}"
                           class="block w-full text-center bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                            {{ __('messages.view_profile') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
