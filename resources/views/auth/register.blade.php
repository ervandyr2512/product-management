<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-2">Daftar Akun</h2>
        <p class="text-sm text-gray-600 text-center">Buat akun untuk mulai konsultasi</p>
    </div>

    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-800">
                    Setelah mendaftar, Anda akan menerima email verifikasi. Mohon verifikasi email Anda untuk dapat mengakses semua fitur.
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" value="Nomor Telepon (WhatsApp)" />
            <div class="flex gap-2">
                <!-- Country Code Dropdown -->
                <select name="country_code" id="country_code" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 w-32" required>
                    <option value="62" {{ old('country_code', '62') == '62' ? 'selected' : '' }}>🇮🇩 +62</option>
                    <option value="60" {{ old('country_code') == '60' ? 'selected' : '' }}>🇲🇾 +60</option>
                    <option value="65" {{ old('country_code') == '65' ? 'selected' : '' }}>🇸🇬 +65</option>
                    <option value="66" {{ old('country_code') == '66' ? 'selected' : '' }}>🇹🇭 +66</option>
                    <option value="84" {{ old('country_code') == '84' ? 'selected' : '' }}>🇻🇳 +84</option>
                    <option value="63" {{ old('country_code') == '63' ? 'selected' : '' }}>🇵🇭 +63</option>
                    <option value="1" {{ old('country_code') == '1' ? 'selected' : '' }}>🇺🇸 +1</option>
                    <option value="44" {{ old('country_code') == '44' ? 'selected' : '' }}>🇬🇧 +44</option>
                </select>

                <!-- Phone Number Input -->
                <x-text-input id="phone" class="flex-1" type="text" name="phone" :value="old('phone')" required placeholder="81234567890" />
            </div>
            <p class="text-xs text-gray-500 mt-1">Masukkan nomor tanpa diawali 0 atau kode negara. Contoh: 81234567890 untuk +62 81234567890</p>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            <x-input-error :messages="$errors->get('country_code')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
