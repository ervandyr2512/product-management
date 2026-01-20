<x-admin-layout>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900">Professional Management</h1>
                <p class="text-gray-600 mt-1">Manage all professionals and their profiles</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.professionals.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">All Types</option>
                            <option value="psychiatrist" {{ request('type') === 'psychiatrist' ? 'selected' : '' }}>Psychiatrist</option>
                            <option value="psychologist" {{ request('type') === 'psychologist' ? 'selected' : '' }}>Psychologist</option>
                            <option value="conversationalist" {{ request('type') === 'conversationalist' ? 'selected' : '' }}>Conversationalist</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition mr-2">
                            Filter
                        </button>
                        <a href="{{ route('admin.professionals.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-300 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Professionals Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Professional</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($professionals as $professional)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($professional->profile_photo)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $professional->profile_photo) }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                    <span class="text-purple-600 font-medium">{{ substr($professional->user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $professional->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $professional->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($professional->type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $professional->experience_years }} years
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Rp {{ number_format($professional->rate_30min, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.professionals.toggle-status', $professional) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $professional->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $professional->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                    <a href="{{ route('admin.professionals.show', $professional) }}" class="text-purple-600 hover:text-purple-900">View</a>
                                    <a href="{{ route('admin.professionals.edit', $professional) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>

                                    <form action="{{ route('admin.users.demote', $professional->user) }}" method="POST" class="inline" onsubmit="return confirm('Demote {{ $professional->user->name }} ke user biasa? Profil professional akan dihapus.')">
                                        @csrf
                                        <button type="submit" class="text-orange-600 hover:text-orange-900">
                                            ⬇️ Demote
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.professionals.destroy', $professional) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No professionals found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t">
                {{ $professionals->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
