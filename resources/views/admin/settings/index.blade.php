<x-admin-layout>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Landing Page Settings</h1>
                    <p class="text-gray-600 mt-1">Manage homepage content and appearance</p>
                </div>
                <a href="{{ route('admin.settings.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                    Add New Setting
                </a>
            </div>
        </div>

        <!-- Settings Form -->
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf

            @foreach($settings as $group => $groupSettings)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4 capitalize">{{ str_replace('_', ' ', $group) }}</h2>

                        <div class="space-y-4">
                            @foreach($groupSettings as $setting)
                                <div class="border-b pb-4 last:border-b-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <label for="setting_{{ $setting->key }}" class="block text-sm font-medium text-gray-700">
                                            {{ ucfirst(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>

                                    @if($setting->type === 'text')
                                        <input type="text" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" value="{{ old('settings.' . $setting->key, $setting->value) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">

                                    @elseif($setting->type === 'textarea')
                                        <textarea name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('settings.' . $setting->key, $setting->value) }}</textarea>

                                    @elseif($setting->type === 'number')
                                        <input type="number" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" value="{{ old('settings.' . $setting->key, $setting->value) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">

                                    @elseif($setting->type === 'image')
                                        <div class="space-y-2">
                                            @if($setting->value)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="" class="h-32 w-auto rounded">
                                                </div>
                                            @endif
                                            <input type="file" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" accept="image/*" class="mt-1 block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4
                                                file:rounded-md file:border-0
                                                file:text-sm file:font-semibold
                                                file:bg-purple-50 file:text-purple-700
                                                hover:file:bg-purple-100">
                                            <p class="text-xs text-gray-500">Upload new image to replace current one</p>
                                        </div>
                                    @endif

                                    <p class="text-xs text-gray-500 mt-1">Type: {{ $setting->type }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            @if($settings->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 flex justify-end">
                        <button type="submit" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                            Save All Changes
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        No settings found. Click "Add New Setting" to create one.
                    </div>
                </div>
            @endif
        </form>
    </div>
</x-admin-layout>
