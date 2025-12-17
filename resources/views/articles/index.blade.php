<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Artikel Kesehatan Mental
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filter -->
            <div class="mb-8 bg-white rounded-lg shadow-sm p-6">
                <form action="{{ route('articles.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari artikel..."
                               class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div class="w-full md:w-64">
                        <select name="category"
                                class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                            <option value="all">Semua Kategori</option>
                            <option value="mental_health" {{ request('category') == 'mental_health' ? 'selected' : '' }}>Kesehatan Mental</option>
                            <option value="anxiety" {{ request('category') == 'anxiety' ? 'selected' : '' }}>Kecemasan</option>
                            <option value="depression" {{ request('category') == 'depression' ? 'selected' : '' }}>Depresi</option>
                            <option value="stress" {{ request('category') == 'stress' ? 'selected' : '' }}>Stress</option>
                            <option value="self_care" {{ request('category') == 'self_care' ? 'selected' : '' }}>Perawatan Diri</option>
                            <option value="therapy" {{ request('category') == 'therapy' ? 'selected' : '' }}>Terapi</option>
                            <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                        Cari
                    </button>
                </form>
            </div>

            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($articles as $article)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden">
                            @if($article->featured_image)
                                <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-purple-400 to-indigo-500 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <div class="p-6">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                        {{ $article->category_label }}
                                    </span>
                                    <span class="text-gray-500 text-sm">{{ $article->published_at->format('d M Y') }}</span>
                                </div>

                                <h3 class="font-bold text-xl mb-2 text-gray-900 line-clamp-2">
                                    {{ $article->title }}
                                </h3>

                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    {{ $article->excerpt }}
                                </p>

                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">{{ $article->author }}</span>
                                    <a href="{{ route('articles.show', $article->slug) }}"
                                       class="text-purple-600 hover:text-purple-700 font-semibold text-sm">
                                        Baca Selengkapnya →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada artikel ditemukan</h3>
                    <p class="mt-1 text-gray-500">Coba gunakan kata kunci atau filter yang berbeda</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
