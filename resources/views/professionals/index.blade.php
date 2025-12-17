<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Temukan Professional Anda
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filter -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <form method="GET" action="{{ route('professionals.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Search Input -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                            <input type="text" name="search" placeholder="Nama atau spesialisasi..."
                                   value="{{ request('search') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        <!-- Type Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Professional</label>
                            <select name="type" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                                <option value="">Semua Tipe</option>
                                <option value="psychiatrist" {{ request('type') == 'psychiatrist' ? 'selected' : '' }}>Psikiater</option>
                                <option value="psychologist" {{ request('type') == 'psychologist' ? 'selected' : '' }}>Psikolog</option>
                                <option value="conversationalist" {{ request('type') == 'conversationalist' ? 'selected' : '' }}>Conversationalist</option>
                            </select>
                        </div>

                        <!-- Sort Dropdown -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                            <select name="sort" class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" onchange="this.form.submit()">
                                <option value="">-- Pilih Urutan --</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama: A to Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama: Z to A</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga: Termurah</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga: Termahal</option>
                            </select>
                        </div>

                        <!-- Search Button -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                            <button type="submit" class="w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                                Cari Professional
                            </button>
                        </div>
                    </div>

                    <!-- Active Filters Display -->
                    @if(request('search') || request('type') || request('sort'))
                        <div class="mt-4 flex items-center gap-2 flex-wrap">
                            <span class="text-sm text-gray-600">Filter aktif:</span>

                            @if(request('search'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                                    Pencarian: "{{ request('search') }}"
                                    <a href="{{ route('professionals.index', array_merge(request()->except('search'), ['type' => request('type'), 'sort' => request('sort')])) }}" class="ml-2 text-purple-600 hover:text-purple-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            @if(request('type'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                                    Tipe: {{ request('type') == 'psychiatrist' ? 'Psikiater' : (request('type') == 'psychologist' ? 'Psikolog' : 'Conversationalist') }}
                                    <a href="{{ route('professionals.index', array_merge(request()->except('type'), ['search' => request('search'), 'sort' => request('sort')])) }}" class="ml-2 text-purple-600 hover:text-purple-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            @if(request('sort'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800">
                                    Urutan:
                                    @if(request('sort') == 'name_asc') A to Z
                                    @elseif(request('sort') == 'name_desc') Z to A
                                    @elseif(request('sort') == 'price_asc') Termurah
                                    @elseif(request('sort') == 'price_desc') Termahal
                                    @endif
                                    <a href="{{ route('professionals.index', array_merge(request()->except('sort'), ['search' => request('search'), 'type' => request('type')])) }}" class="ml-2 text-purple-600 hover:text-purple-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </a>
                                </span>
                            @endif

                            <a href="{{ route('professionals.index') }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                Reset semua filter
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Professionals Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($professionals as $professional)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-indigo-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    {{ strtoupper(substr($professional->user->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-bold text-lg text-gray-900">{{ $professional->user->name }}</h3>
                                    <p class="text-sm text-purple-600 font-medium">
                                        @if($professional->type === 'psychiatrist')
                                            Psikiater
                                        @elseif($professional->type === 'psychologist')
                                            Psikolog
                                        @else
                                            Conversationalist
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-gray-600 text-sm mb-2">
                                    <span class="font-semibold text-gray-700">Spesialisasi:</span> {{ $professional->specialization }}
                                </p>
                                <p class="text-gray-600 text-sm">
                                    <span class="font-semibold text-gray-700">Pengalaman:</span> {{ $professional->experience_years }} tahun
                                </p>
                            </div>

                            <div class="border-t pt-4 mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">30 menit</span>
                                    <span class="font-bold text-purple-600">Rp {{ number_format($professional->rate_30min, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">60 menit</span>
                                    <span class="font-bold text-purple-600">Rp {{ number_format($professional->rate_60min, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <a href="{{ route('professionals.show', $professional) }}"
                               class="block w-full text-center bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                                Lihat Detail & Jadwal
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3">
                        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada professional ditemukan</h3>
                            <p class="mt-1 text-gray-500">Coba gunakan kata kunci atau filter yang berbeda</p>
                        </div>
                    </div>
                @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $professionals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
