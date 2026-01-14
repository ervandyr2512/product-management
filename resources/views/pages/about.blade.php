<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.about_us') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-12 text-white mb-8">
                <h1 class="text-4xl font-bold mb-4">Teman Bicara</h1>
                <p class="text-xl text-purple-100">{{ __('messages.footer_tagline') }}</p>
            </div>

            <!-- Mission & Vision -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">{{ __('messages.about_mission_title') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('messages.about_mission_desc') }}
                    </p>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">{{ __('messages.about_vision_title') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('messages.about_vision_desc') }}
                    </p>
                </div>
            </div>

            <!-- Our Values -->
            <div class="mb-12">
                <h2 class="text-3xl font-bold mb-8 text-center text-gray-900 dark:text-gray-100">{{ __('messages.about_values_title') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.about_value1_title') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('messages.about_value1_desc') }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.about_value2_title') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('messages.about_value2_desc') }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 text-center">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-gray-100">{{ __('messages.about_value3_title') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ __('messages.about_value3_desc') }}</p>
                    </div>
                </div>
            </div>

            <!-- Team Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                <h2 class="text-3xl font-bold mb-6 text-center text-gray-900 dark:text-gray-100">{{ __('messages.about_team_title') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 text-center mb-8 max-w-3xl mx-auto">
                    {{ __('messages.about_team_desc') }}
                </p>
                <div class="text-center">
                    <a href="{{ route('professionals.index') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        {{ __('messages.view_professionals') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
