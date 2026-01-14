<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Professional Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Professional Details</h1>
                    <div class="space-x-2">
                        <a href="{{ route('admin.professionals.edit', $professional) }}" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition">
                            Edit Professional
                        </a>
                        <a href="{{ route('admin.professionals.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                            Back
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Name</h3>
                        <p class="mt-1 text-gray-900">{{ $professional->user->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Email</h3>
                        <p class="mt-1 text-gray-900">{{ $professional->user->email }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Type</h3>
                        <p class="mt-1 text-gray-900">{{ ucfirst($professional->type) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">License Number</h3>
                        <p class="mt-1 text-gray-900">{{ $professional->license_number }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Specialization</h3>
                        <p class="mt-1 text-gray-900">{{ $professional->specialization ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Experience</h3>
                        <p class="mt-1 text-gray-900">{{ $professional->experience_years }} years</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Rate 30 Min</h3>
                        <p class="mt-1 text-gray-900">Rp {{ number_format($professional->rate_30min, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Rate 60 Min</h3>
                        <p class="mt-1 text-gray-900">Rp {{ number_format($professional->rate_60min, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                        <p class="mt-1">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $professional->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $professional->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Average Rating</h3>
                        <p class="mt-1 text-gray-900">{{ number_format($professional->averageRating(), 1) }} / 5.0 ({{ $professional->totalReviews() }} reviews)</p>
                    </div>
                    <div class="col-span-2">
                        <h3 class="text-sm font-medium text-gray-500">Bio</h3>
                        <p class="mt-1 text-gray-900">{{ $professional->bio ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedules -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedules ({{ $professional->schedules->count() }})</h2>
                @if($professional->schedules->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($professional->schedules->take(10) as $schedule)
                            <div class="border rounded p-3">
                                <p class="text-sm font-medium text-gray-900">{{ $schedule->date->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">{{ $schedule->start_time }} - {{ $schedule->end_time }}</p>
                                <span class="text-xs {{ $schedule->is_available ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $schedule->is_available ? 'Available' : 'Booked' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No schedules yet</p>
                @endif
            </div>
        </div>

        <!-- Appointments -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Appointments ({{ $professional->appointments->count() }})</h2>
                @if($professional->appointments->count() > 0)
                    <div class="space-y-3">
                        @foreach($professional->appointments->take(10) as $appointment)
                            <div class="flex justify-between items-center border-b pb-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $appointment->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $appointment->appointment_date->format('d M Y') }} - {{ $appointment->start_time }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $appointment->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No appointments yet</p>
                @endif
            </div>
        </div>

        <!-- Reviews -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Reviews ({{ $professional->reviews->count() }})</h2>
                @if($professional->reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($professional->reviews->take(5) as $review)
                            <div class="border-b pb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-medium text-gray-900">{{ $review->user->name }}</p>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No reviews yet</p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
