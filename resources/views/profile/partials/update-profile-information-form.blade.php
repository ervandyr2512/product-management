<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Perbarui informasi profil akun Anda termasuk email dan nomor WhatsApp untuk notifikasi.
        </p>
    </header>

    <!-- WhatsApp Notification Info -->
    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-800">
                    <strong>Penting:</strong> Pastikan nomor WhatsApp Anda aktif dan terhubung. Anda akan menerima notifikasi konfirmasi pembayaran dan detail janji temu melalui WhatsApp dan email.
                </p>
            </div>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" value="Nomor WhatsApp" />
            @php
                // Parse existing phone number to separate country code
                $currentPhone = old('phone', $user->phone);
                $countryCode = '62'; // default
                $phoneNumber = '';

                if ($currentPhone) {
                    // Check if starts with known country codes
                    if (str_starts_with($currentPhone, '62')) {
                        $countryCode = '62';
                        $phoneNumber = substr($currentPhone, 2);
                    } elseif (str_starts_with($currentPhone, '60')) {
                        $countryCode = '60';
                        $phoneNumber = substr($currentPhone, 2);
                    } elseif (str_starts_with($currentPhone, '65')) {
                        $countryCode = '65';
                        $phoneNumber = substr($currentPhone, 2);
                    } elseif (str_starts_with($currentPhone, '66')) {
                        $countryCode = '66';
                        $phoneNumber = substr($currentPhone, 2);
                    } elseif (str_starts_with($currentPhone, '84')) {
                        $countryCode = '84';
                        $phoneNumber = substr($currentPhone, 2);
                    } elseif (str_starts_with($currentPhone, '63')) {
                        $countryCode = '63';
                        $phoneNumber = substr($currentPhone, 2);
                    } elseif (str_starts_with($currentPhone, '1')) {
                        $countryCode = '1';
                        $phoneNumber = substr($currentPhone, 1);
                    } elseif (str_starts_with($currentPhone, '44')) {
                        $countryCode = '44';
                        $phoneNumber = substr($currentPhone, 2);
                    } else {
                        // If no match, assume it's already in correct format
                        $phoneNumber = $currentPhone;
                    }
                }

                $countryCode = old('country_code', $countryCode);
                $phoneNumber = old('phone_only', $phoneNumber);
            @endphp

            <div class="flex gap-2">
                <!-- Country Code Dropdown -->
                <select name="country_code" id="country_code" class="rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 w-32" required>
                    <option value="62" {{ $countryCode == '62' ? 'selected' : '' }}>🇮🇩 +62</option>
                    <option value="60" {{ $countryCode == '60' ? 'selected' : '' }}>🇲🇾 +60</option>
                    <option value="65" {{ $countryCode == '65' ? 'selected' : '' }}>🇸🇬 +65</option>
                    <option value="66" {{ $countryCode == '66' ? 'selected' : '' }}>🇹🇭 +66</option>
                    <option value="84" {{ $countryCode == '84' ? 'selected' : '' }}>🇻🇳 +84</option>
                    <option value="63" {{ $countryCode == '63' ? 'selected' : '' }}>🇵🇭 +63</option>
                    <option value="1" {{ $countryCode == '1' ? 'selected' : '' }}>🇺🇸 +1</option>
                    <option value="44" {{ $countryCode == '44' ? 'selected' : '' }}>🇬🇧 +44</option>
                </select>

                <!-- Phone Number Input -->
                <x-text-input id="phone" name="phone" type="text" class="flex-1" :value="$phoneNumber" required placeholder="81234567890" />
            </div>
            <p class="text-xs text-gray-500 mt-1">Masukkan nomor tanpa diawali 0 atau kode negara. Contoh: 81234567890 untuk +62 81234567890</p>
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            <x-input-error class="mt-2" :messages="$errors->get('country_code')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
