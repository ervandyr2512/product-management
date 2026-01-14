<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.appointment_details') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="border-b dark:border-gray-700 pb-4 mb-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $appointment->professional->user->name }}</h3>
                            <span class="px-3 py-1 rounded text-sm font-semibold
                                @if($appointment->status == 'confirmed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($appointment->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($appointment->status == 'completed') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">{{ ucfirst($appointment->professional->type) }}</p>
                    </div>

                    {{-- Video Consultation Section --}}
                    @if($appointment->status == 'confirmed' && $appointment->payment && $appointment->payment->status == 'completed')
                        @php
                            $now = now();
                            $appointmentTime = $appointment->appointment_date->format('Y-m-d') . ' ' . $appointment->schedule->start_time;
                            $appointmentDateTime = \Carbon\Carbon::parse($appointmentTime);
                            $canJoinFrom = $appointmentDateTime->copy()->subMinutes(10);
                            $canJoinUntil = $appointmentDateTime->copy()->addMinutes(30);
                            $canJoin = $appointment->canStartVideoChat();
                            $minutesUntilStart = $now->diffInMinutes($appointmentDateTime, false);
                        @endphp

                        <div class="mb-6 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900 dark:to-indigo-900 border border-purple-200 dark:border-purple-700 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-lg text-purple-900 dark:text-purple-100">{{ __('messages.video_consultation') }}</h4>
                                    @if($canJoin)
                                        <p class="text-sm text-purple-700 dark:text-purple-300">{{ __('messages.can_join_now') }}</p>
                                    @elseif($minutesUntilStart > 0 && $minutesUntilStart <= 60)
                                        <p class="text-sm text-purple-700 dark:text-purple-300">
                                            {{ __('messages.consultation_starts_in') }} {{ $minutesUntilStart }} {{ __('messages.minutes') }}
                                        </p>
                                    @else
                                        <p class="text-sm text-purple-700 dark:text-purple-300">{{ $appointmentDateTime->format('d M Y, H:i') }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($canJoin)
                                <a href="{{ route('video-chat.show', $appointment) }}"
                                   class="inline-flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-semibold px-8 py-3 rounded-lg transition shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    {{ __('messages.join_video_consultation') }}
                                </a>
                            @else
                                <button disabled
                                        class="inline-flex items-center justify-center bg-gray-400 text-white font-semibold px-8 py-3 rounded-lg cursor-not-allowed opacity-60">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ __('messages.video_chat_not_ready') }}
                                </button>
                            @endif

                            <p class="text-xs text-purple-600 dark:text-purple-400 mt-3">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('messages.video_chat_not_ready') }}
                            </p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.date_and_time') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400">{{ $appointment->appointment_date->format('d F Y') }}</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.duration') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400">{{ $appointment->duration }} {{ __('messages.minutes') }}</p>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.price') }}</h4>
                                <p class="text-gray-600 dark:text-gray-400">Rp {{ number_format($appointment->price, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @if($appointment->payment)
                                <div>
                                    <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.payment_status') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ ucfirst($appointment->payment->status) }}</p>
                                    @if($appointment->payment->paid_at)
                                        <p class="text-sm text-gray-500 dark:text-gray-500">{{ __('messages.paid_at') }}: {{ $appointment->payment->paid_at->format('d F Y H:i') }}</p>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.payment_method') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $appointment->payment->payment_method)) }}</p>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ __('messages.transaction_id') }}</h4>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $appointment->payment->payment_gateway_id }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($appointment->notes)
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('messages.notes') }}</h4>
                            <p class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 p-4 rounded">{{ $appointment->notes }}</p>
                        </div>
                    @endif

                    <div class="flex gap-3">
                        <a href="{{ route('appointments.index') }}"
                           class="bg-gray-600 dark:bg-gray-700 text-white px-6 py-2 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition">
                            {{ __('messages.back_to_list') }}
                        </a>
                        @if(in_array($appointment->status, ['pending', 'confirmed']))
                            <form action="{{ route('appointments.cancel', $appointment) }}" method="POST"
                                  onsubmit="return confirm('{{ __('messages.confirm_cancel_appointment') }}')">
                                @csrf
                                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                    {{ __('messages.cancel_appointment') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
