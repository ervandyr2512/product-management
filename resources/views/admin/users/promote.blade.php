<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Promote User to Professional</h1>
                        <p class="text-gray-600 mt-1">Setup professional profile for: <span class="font-semibold">{{ $user->name }}</span></p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
                        ← Back to Users
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.users.promote', $user) }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900">Professional Information</h3>

                    <!-- Professional Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                            Professional Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Type</option>
                            <option value="psychiatrist" {{ old('type') === 'psychiatrist' ? 'selected' : '' }}>Psychiatrist (Psikiater)</option>
                            <option value="psychologist" {{ old('type') === 'psychologist' ? 'selected' : '' }}>Psychologist (Psikolog)</option>
                            <option value="conversationalist" {{ old('type') === 'conversationalist' ? 'selected' : '' }}>Conversationalist (Teman Bicara)</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Specialization -->
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-1">
                            Specialization <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="specialization" name="specialization" value="{{ old('specialization') }}" required
                            placeholder="e.g., Anxiety Disorders, Depression, Trauma"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        @error('specialization')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- License Number -->
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">
                            License Number
                        </label>
                        <input type="text" id="license_number" name="license_number" value="{{ old('license_number') }}"
                            placeholder="e.g., PSI-123456 or STR-987654"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <p class="mt-1 text-sm text-gray-500">Optional - Professional license or certification number</p>
                        @error('license_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Experience Years -->
                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">
                            Years of Experience <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="experience_years" name="experience_years" value="{{ old('experience_years', 0) }}" required min="0"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        @error('experience_years')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Education -->
                    <div>
                        <label for="education" class="block text-sm font-medium text-gray-700 mb-1">
                            Education
                        </label>
                        <textarea id="education" name="education" rows="3"
                            placeholder="e.g., S1 Psychology - University of Indonesia&#10;S2 Clinical Psychology - Gadjah Mada University"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('education') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Enter education background, one per line</p>
                        @error('education')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Languages -->
                    <div>
                        <label for="languages" class="block text-sm font-medium text-gray-700 mb-1">
                            Languages
                        </label>
                        <input type="text" id="languages" name="languages" value="{{ old('languages', 'Indonesian, English') }}"
                            placeholder="e.g., Indonesian, English, Mandarin"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <p class="mt-1 text-sm text-gray-500">Comma-separated list of languages</p>
                        @error('languages')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">
                            Professional Bio <span class="text-red-500">*</span>
                        </label>
                        <textarea id="bio" name="bio" rows="5" required
                            placeholder="Write a professional biography that will be displayed on the professional's profile. Include expertise, approach, and what clients can expect."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('bio') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">This will be visible to users on the professional's profile</p>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Pricing & Session Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <h3 class="text-lg font-semibold text-gray-900">Pricing & Session Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Price per Session -->
                        <div>
                            <label for="price_per_session" class="block text-sm font-medium text-gray-700 mb-1">
                                Price per Session (IDR) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="price_per_session" name="price_per_session" value="{{ old('price_per_session', 150000) }}" required min="0" step="1000"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <p class="mt-1 text-sm text-gray-500">Recommended: Rp 150,000 - Rp 500,000</p>
                            @error('price_per_session')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Session Duration -->
                        <div>
                            <label for="session_duration" class="block text-sm font-medium text-gray-700 mb-1">
                                Session Duration (minutes) <span class="text-red-500">*</span>
                            </label>
                            <select id="session_duration" name="session_duration" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="30" {{ old('session_duration') == 30 ? 'selected' : '' }}>30 minutes</option>
                                <option value="45" {{ old('session_duration') == 45 ? 'selected' : '' }}>45 minutes</option>
                                <option value="60" {{ old('session_duration', 60) == 60 ? 'selected' : '' }}>60 minutes</option>
                                <option value="90" {{ old('session_duration') == 90 ? 'selected' : '' }}>90 minutes</option>
                                <option value="120" {{ old('session_duration') == 120 ? 'selected' : '' }}>120 minutes</option>
                            </select>
                            @error('session_duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                        ✅ Promote to Professional
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
