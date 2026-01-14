<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- User Info -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
                    <div class="space-x-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition">
                            Edit User
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                            Back
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Name</h3>
                        <p class="mt-1 text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Email</h3>
                        <p class="mt-1 text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Phone</h3>
                        <p class="mt-1 text-gray-900">{{ $user->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Role</h3>
                        <p class="mt-1">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $user->role === 'user' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Email Verified</h3>
                        <p class="mt-1 text-gray-900">{{ $user->email_verified_at ? 'Yes' : 'No' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Member Since</h3>
                        <p class="mt-1 text-gray-900">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Appointments ({{ $user->appointments->count() }})</h2>
                @if($user->appointments->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->appointments->take(5) as $appointment)
                            <div class="flex justify-between items-center border-b pb-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">With {{ $appointment->professional->user->name }}</p>
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

        <!-- Payments -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Payments ({{ $user->payments->count() }})</h2>
                @if($user->payments->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->payments->take(5) as $payment)
                            <div class="flex justify-between items-center border-b pb-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->payment_method }} - {{ $payment->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $payment->payment_status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $payment->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $payment->payment_status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($payment->payment_status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No payments yet</p>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
