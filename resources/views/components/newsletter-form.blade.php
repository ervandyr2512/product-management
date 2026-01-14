<div class="bg-gradient-to-r from-purple-600 to-indigo-600 dark:from-purple-700 dark:to-indigo-700 rounded-lg p-8 shadow-lg">
    <div class="max-w-2xl mx-auto text-center">
        <h3 class="text-2xl font-bold text-white mb-3">
            {{ __('messages.newsletter_title') }}
        </h3>
        <p class="text-purple-100 mb-6">
            {{ __('messages.newsletter_description') }}
        </p>

        <form id="newsletter-form" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input
                type="email"
                name="email"
                id="newsletter-email"
                placeholder="{{ __('messages.newsletter_email') }}"
                required
                class="flex-1 px-4 py-3 rounded-lg border-0 focus:ring-2 focus:ring-purple-300 dark:bg-gray-800 dark:text-white"
            >
            <button
                type="submit"
                class="px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200 focus:ring-2 focus:ring-white"
            >
                {{ __('messages.newsletter_subscribe') }}
            </button>
        </form>

        <div id="newsletter-message" class="mt-4 hidden">
            <p class="text-white font-medium"></p>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        document.getElementById('newsletter-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const email = form.querySelector('#newsletter-email').value;
            const submitBtn = form.querySelector('button[type="submit"]');
            const messageDiv = document.getElementById('newsletter-message');
            const messageText = messageDiv.querySelector('p');

            // Disable button during submission
            submitBtn.disabled = true;
            submitBtn.textContent = '{{ __("messages.loading") }}';

            try {
                const response = await fetch('{{ route("newsletter.subscribe") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email })
                });

                const data = await response.json();

                messageDiv.classList.remove('hidden');
                messageText.textContent = data.message;

                if (data.success) {
                    form.reset();
                    messageDiv.classList.add('bg-green-500', 'bg-opacity-20', 'rounded-lg', 'p-3');
                } else {
                    messageDiv.classList.add('bg-red-500', 'bg-opacity-20', 'rounded-lg', 'p-3');
                }

                // Hide message after 5 seconds
                setTimeout(() => {
                    messageDiv.classList.add('hidden');
                    messageDiv.classList.remove('bg-green-500', 'bg-red-500', 'bg-opacity-20', 'rounded-lg', 'p-3');
                }, 5000);

            } catch (error) {
                console.error('Newsletter subscription error:', error);
                messageDiv.classList.remove('hidden');
                messageDiv.classList.add('bg-red-500', 'bg-opacity-20', 'rounded-lg', 'p-3');
                messageText.textContent = '{{ __("messages.error") }}';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = '{{ __("messages.newsletter_subscribe") }}';
            }
        });
    </script>
    @endpush
@endonce
