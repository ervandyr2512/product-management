<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('articles.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Artikel
                </a>
            </div>

            <!-- Article Content -->
            <article class="bg-white rounded-lg shadow-sm overflow-hidden">
                @if($article->featured_image)
                    <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-96 object-cover">
                @else
                    <div class="w-full h-96 bg-gradient-to-br from-purple-400 to-indigo-500"></div>
                @endif

                <div class="p-8">
                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <span class="bg-purple-100 text-purple-800 text-sm font-semibold px-3 py-1 rounded-full">
                            {{ $article->category_label }}
                        </span>
                        <span class="text-gray-500">{{ $article->published_at->format('d F Y') }}</span>
                        <span class="text-gray-500">•</span>
                        <span class="text-gray-500">{{ $article->author }}</span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-4xl font-bold text-gray-900 mb-6">
                        {{ $article->title }}
                    </h1>

                    <!-- Excerpt -->
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        {{ $article->excerpt }}
                    </p>

                    <!-- Content -->
                    <div class="prose prose-lg max-w-none">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>
            </article>

            <!-- Related Articles -->
            @if($relatedArticles->count() > 0)
                <div class="mt-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Artikel Terkait</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedArticles as $related)
                            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden">
                                @if($related->featured_image)
                                    <img src="{{ $related->featured_image }}" alt="{{ $related->title }}" class="w-full h-40 object-cover">
                                @else
                                    <div class="w-full h-40 bg-gradient-to-br from-purple-400 to-indigo-500"></div>
                                @endif

                                <div class="p-4">
                                    <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-0.5 rounded">
                                        {{ $related->category_label }}
                                    </span>
                                    <h3 class="font-semibold text-lg mt-2 mb-2 line-clamp-2">
                                        {{ $related->title }}
                                    </h3>
                                    <a href="{{ route('articles.show', $related->slug) }}"
                                       class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                                        Baca →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
