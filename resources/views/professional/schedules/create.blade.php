<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.create_new_schedule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
                <form action="{{ route('professional.schedules.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="date" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">{{ __('messages.select_schedule_date') }}</label>
                        <input type="date" id="date" name="date" value="{{ old('date') }}" required
                               min="{{ date('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500">
                        @error('date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="start_time" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">{{ __('messages.select_start_time') }}</label>
                            <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500">
                            @error('start_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-gray-700 dark:text-gray-300 font-medium mb-2">{{ __('messages.select_end_time') }}</label>
                            <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-purple-500 focus:ring-purple-500">
                            @error('end_time')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('professional.schedules.index') }}"
                           class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            {{ __('messages.cancel') }}
                        </a>
                        <button type="submit"
                                class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            {{ __('messages.save_schedule') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
