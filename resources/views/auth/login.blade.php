<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col gap-4 mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-800">School Management System Login</h1>
                <p class="text-gray-500">Enter your email and password to access your account</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="!text-md text-gray-700" />
                    <x-text-input 
                        id="email" 
                        class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required autofocus autocomplete="username" 
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" class="!text-md text-gray-700" />
                    <x-text-input 
                        id="password" 
                        class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                        type="password" 
                        name="password" 
                        required autocomplete="current-password" 
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ml-2 text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <!-- Forgot Password & Register -->
                <div class="flex flex-col items-start gap-2">
                    @if (Route::has('password.request'))
                        <a class="text-indigo-600 hover:text-indigo-800 text-sm underline" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <x-primary-button class="w-full py-3 rounded-full flex justify-center items-center bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold transition">
                    {{ __('Sign in') }}
                </x-primary-button>

            </form>
        </div>
    </div>
</x-guest-layout>
