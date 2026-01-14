<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.messages') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if($conversations->count() > 0)
                        <div class="space-y-4">
                            @foreach($conversations as $conversation)
                                @if($conversation['user'])
                                    <a href="{{ route('chat.show', $conversation['user']->id) }}" class="block">
                                        <div class="flex items-center p-4 border dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <div class="flex-shrink-0 mr-4">
                                                @if($conversation['user']->profile_photo)
                                                    <img src="{{ asset('storage/' . $conversation['user']->profile_photo) }}" alt="" class="w-12 h-12 rounded-full object-cover">
                                                @else
                                                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                                        <span class="text-purple-600 dark:text-purple-400 font-medium">
                                                            {{ substr($conversation['user']->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                        {{ $conversation['user']->name }}
                                                        @if($conversation['user']->role === 'professional' && $conversation['user']->professional)
                                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                - {{ ucfirst($conversation['user']->professional->type) }}
                                                            </span>
                                                        @endif
                                                    </p>
                                                    @if($conversation['last_message'])
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $conversation['last_message']->created_at->diffForHumans() }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="flex items-center justify-between mt-1">
                                                    @if($conversation['last_message'])
                                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                            {{ Str::limit($conversation['last_message']->message, 50) }}
                                                        </p>
                                                    @else
                                                        <p class="text-sm text-gray-400 dark:text-gray-500 italic">
                                                            {{ __('messages.no_messages_yet') }}
                                                        </p>
                                                    @endif
                                                    @if($conversation['unread_count'] > 0)
                                                        <span class="ml-2 bg-purple-600 text-white text-xs px-2 py-1 rounded-full">
                                                            {{ $conversation['unread_count'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('messages.no_messages') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('messages.start_conversation') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
