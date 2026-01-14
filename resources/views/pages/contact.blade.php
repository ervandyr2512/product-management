<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.contact_page_title') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Contact Form -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                    <h2 class="text-2xl font-bold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.contact_form_title') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.contact_form_desc') }}</p>

                    @if(session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">{{ __('messages.contact_name') }}</label>
                            <input type="text" id="name" name="name" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="{{ __('messages.contact_name_placeholder') }}">
                            @error('name')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">{{ __('messages.contact_email_title') }}</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="email@example.com">
                            @error('email')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">{{ __('messages.contact_phone_title') }}</label>
                            <input type="text" id="phone" name="phone"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="+62">
                            @error('phone')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">{{ __('messages.contact_subject') }}</label>
                            <select id="subject" name="subject" required
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500">
                                <option value="">{{ __('messages.contact_subject_select') }}</option>
                                <option value="general">{{ __('messages.contact_subject_general') }}</option>
                                <option value="booking">{{ __('messages.contact_subject_booking') }}</option>
                                <option value="technical">{{ __('messages.contact_subject_technical') }}</option>
                                <option value="professional">{{ __('messages.contact_subject_professional') }}</option>
                                <option value="partnership">{{ __('messages.contact_subject_partnership') }}</option>
                                <option value="other">{{ __('messages.contact_subject_other') }}</option>
                            </select>
                            @error('subject')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="message" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">{{ __('messages.contact_message') }}</label>
                            <textarea id="message" name="message" rows="5" required
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500"
                                      placeholder="{{ __('messages.contact_message_placeholder') }}"></textarea>
                            @error('message')
                                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                            {{ __('messages.contact_send') }}
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div class="space-y-6">
                    <!-- Contact Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">{{ __('messages.contact_info_title') }}</h2>

                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1 text-gray-900 dark:text-gray-100">{{ __('messages.contact_email_title') }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">info@temanbicara.id</p>
                                    <p class="text-gray-600 dark:text-gray-400">support@temanbicara.id</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1 text-gray-900 dark:text-gray-100">{{ __('messages.contact_phone_title') }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">+62 21 1234 5678</p>
                                    <p class="text-gray-600 dark:text-gray-400">+62 812 3456 7890</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1 text-gray-900 dark:text-gray-100">{{ __('messages.contact_address') }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{!! nl2br(e(__('messages.contact_address_value'))) !!}</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold mb-1 text-gray-900 dark:text-gray-100">{{ __('messages.contact_hours_title') }}</h3>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.contact_hours_weekday') }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ __('messages.contact_hours_weekend') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Quick Links -->
                    <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg shadow-sm p-8 text-white">
                        <h2 class="text-2xl font-bold mb-4">{{ __('messages.contact_faq_title') }}</h2>
                        <p class="mb-6 text-purple-100">{{ __('messages.contact_faq_desc') }}</p>
                        <a href="{{ route('articles.index') }}" class="inline-block bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                            {{ __('messages.contact_faq_link') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
