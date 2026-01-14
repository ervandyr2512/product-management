<x-admin-layout>
    <div class="max-w-2xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Add New Setting</h1>

                <form method="POST" action="{{ route('admin.settings.store') }}">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="key" class="block text-sm font-medium text-gray-700">Key</label>
                            <input type="text" name="key" id="key" value="{{ old('key') }}" required placeholder="e.g., hero_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <p class="text-xs text-gray-500 mt-1">Use lowercase with underscores (e.g., hero_title, stats_users_count)</p>
                            @error('key')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="value" class="block text-sm font-medium text-gray-700">Value</label>
                            <textarea name="value" id="value" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('value') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Initial value for this setting</p>
                            @error('value')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Text (Single Line)</option>
                                <option value="textarea" {{ old('type') === 'textarea' ? 'selected' : '' }}>Textarea (Multiple Lines)</option>
                                <option value="number" {{ old('type') === 'number' ? 'selected' : '' }}>Number</option>
                                <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>Image Upload</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="group" class="block text-sm font-medium text-gray-700">Group</label>
                            <input type="text" name="group" id="group" value="{{ old('group', 'general') }}" required placeholder="e.g., hero, stats, features" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <p class="text-xs text-gray-500 mt-1">Group settings by section (e.g., hero, stats, features, testimonials)</p>
                            @error('group')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <a href="{{ route('admin.settings.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                                Cancel
                            </a>
                            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition">
                                Create Setting
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
