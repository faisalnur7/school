<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col gap-4 mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-800">{{ __('Reset Password') }}</h1>
                <p class="text-gray-500">{{ __('Enter your new password to secure your account') }}</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="flex flex-col gap-5">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <x-input-label for="email" :value="__('Email')" class="!text-md text-gray-700" />
                    <x-text-input
                        id="email"
                        class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        type="email"
                        name="email"
                        :value="old('email', $request->email)"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" class="!text-md text-gray-700" />
                    <x-text-input
                        id="password"
                        class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="!text-md text-gray-700" />
                    <x-text-input
                        id="password_confirmation"
                        class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
                </div>

                <x-primary-button class="w-full py-3 rounded-full flex justify-center items-center bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold transition">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-guest-layout>
