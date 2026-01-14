@props(['professionalId', 'isFavorited' => false])

<button
    type="button"
    onclick="toggleFavorite({{ $professionalId }}, this)"
    data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
    class="inline-flex items-center justify-center w-10 h-10 rounded-full transition-colors duration-200 {{ $isFavorited ? 'bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900 dark:text-red-300' : 'bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-500 dark:hover:bg-gray-600' }}"
    title="{{ $isFavorited ? __('messages.remove_from_favorites') : __('messages.add_to_favorites') }}"
>
    <svg class="w-6 h-6 {{ $isFavorited ? 'fill-current' : '' }}" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
    </svg>
</button>

@once
    @push('scripts')
    <script>
        function toggleFavorite(professionalId, button) {
            // Check if user is authenticated
            @guest
                window.location.href = '{{ route('login') }}';
                return;
            @endguest

            const isFavorited = button.dataset.favorited === 'true';
            const icon = button.querySelector('svg');

            // Disable button during request
            button.disabled = true;

            fetch(`/favorites/${professionalId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button state
                    button.dataset.favorited = data.favorited ? 'true' : 'false';

                    if (data.favorited) {
                        // Favorited state
                        button.className = 'inline-flex items-center justify-center w-10 h-10 rounded-full transition-colors duration-200 bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900 dark:text-red-300';
                        button.title = '{{ __("messages.remove_from_favorites") }}';
                        icon.setAttribute('fill', 'currentColor');
                    } else {
                        // Not favorited state
                        button.className = 'inline-flex items-center justify-center w-10 h-10 rounded-full transition-colors duration-200 bg-gray-100 text-gray-400 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-500 dark:hover:bg-gray-600';
                        button.title = '{{ __("messages.add_to_favorites") }}';
                        icon.setAttribute('fill', 'none');
                    }

                    // Show success message
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Favorite toggle error:', error);
                showNotification('{{ __("messages.error") }}', 'error');
            })
            .finally(() => {
                button.disabled = false;
            });
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('animate-fade-out');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }
    </script>
    @endpush
@endonce
