<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('chat.index') }}" class="mr-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center">
                @if($user->profile_photo)
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="" class="w-10 h-10 rounded-full object-cover mr-3">
                @else
                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center mr-3">
                        <span class="text-purple-600 dark:text-purple-400 font-medium">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                @endif
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $user->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Messages Container -->
                <div id="messages-container" class="p-6 h-[500px] overflow-y-auto space-y-4">
                    @foreach($messages as $message)
                        <div class="flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md">
                                <div class="flex items-end {{ $message->sender_id == auth()->id() ? 'flex-row-reverse' : 'flex-row' }}">
                                    @if($message->sender_id != auth()->id())
                                        @if($message->sender->profile_photo)
                                            <img src="{{ asset('storage/' . $message->sender->profile_photo) }}" alt="" class="w-8 h-8 rounded-full object-cover {{ $message->sender_id == auth()->id() ? 'ml-2' : 'mr-2' }}">
                                        @else
                                            <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center {{ $message->sender_id == auth()->id() ? 'ml-2' : 'mr-2' }}">
                                                <span class="text-purple-600 dark:text-purple-400 text-xs">{{ substr($message->sender->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    @endif
                                    <div>
                                        <div class="px-4 py-2 rounded-lg {{ $message->sender_id == auth()->id() ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100' }}">
                                            <p class="text-sm">{{ $message->message }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $message->sender_id == auth()->id() ? 'text-right' : 'text-left' }}">
                                            {{ $message->created_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Message Input -->
                <div class="border-t dark:border-gray-700 p-4">
                    <form id="message-form" method="POST" action="{{ route('chat.store', $user) }}" class="flex space-x-2">
                        @csrf
                        <input type="text" name="message" id="message-input" required placeholder="Type a message..." class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-scroll to bottom
        function scrollToBottom() {
            const container = document.getElementById('messages-container');
            container.scrollTop = container.scrollHeight;
        }

        // Initial scroll
        scrollToBottom();

        // Handle form submission with AJAX
        document.getElementById('message-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value;

            if (!message.trim()) return;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Add message to UI
                    addMessageToUI(data.message, true);
                    messageInput.value = '';
                    scrollToBottom();
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });

        function addMessageToUI(message, isSent) {
            const container = document.getElementById('messages-container');
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${isSent ? 'justify-end' : 'justify-start'}`;

            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

            messageDiv.innerHTML = `
                <div class="max-w-xs lg:max-w-md">
                    <div class="flex items-end ${isSent ? 'flex-row-reverse' : 'flex-row'}">
                        <div>
                            <div class="px-4 py-2 rounded-lg ${isSent ? 'bg-purple-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100'}">
                                <p class="text-sm">${message.message}</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ${isSent ? 'text-right' : 'text-left'}">
                                ${time}
                            </p>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(messageDiv);
        }

        // Poll for new messages every 3 seconds
        setInterval(async function() {
            try {
                const response = await fetch('{{ route('chat.fetch', $user) }}');
                const messages = await response.json();

                messages.forEach(message => {
                    // Check if message already exists
                    const exists = document.querySelector(`[data-message-id="${message.id}"]`);
                    if (!exists && message.sender_id !== {{ auth()->id() }}) {
                        addMessageToUI(message, false);
                        scrollToBottom();
                    }
                });
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }, 3000);
    </script>
    @endpush
</x-app-layout>
