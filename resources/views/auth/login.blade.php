<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        @error('invalid_credentials')
            <x-error-alert>{{ $message }}</x-error-alert>
        @enderror
        
        <form method="POST" action="{{ route('login.authenticate') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="email_or_username" :value="__('Email / Username')" />

                <x-input id="email_or_username" class="block mt-1 w-full" type="text" name="email_or_username" :value="old('email_or_username')" required autofocus />

                @error('email_or_username')
                    <x-forms.error-message :message="$message"></x-forms.error-message>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                                
                @error('password')
                    <x-forms.error-message :message="$message"></x-forms.error-message>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="block mt-3 mb-1">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            {{-- Login Button --}}
            <button type="submit" class="px-4 py-2 w-full rounded font-semibold text-center text-white uppercase bg-gray-800 focus:outline-none focus:ring focus:ring-gray-700 focus:ring-opacity-50">
                Login
            </button>

            {{-- Divider --}}
            <div class="line-divider mt-4 mb-1"></div>

            {{-- Links Bottoms --}}
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <a class="block underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
                {{ __('Register new account') }}
            </a>
        </form>
    </x-auth-card>
</x-guest-layout>
