<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col gap-4 mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-800">{{ __('Forgot Password') }}</h1>
                <p class="text-gray-500">
                    {{ __('Enter your email address and we will send you a password reset link.') }}
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" class="!text-md text-gray-700" />
                    <x-text-input
                        id="email"
                        class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <x-primary-button class="w-full py-3 rounded-full flex justify-center items-center bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold transition">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>

                <div class="text-center">
                    <a class="text-indigo-600 hover:text-indigo-800 text-sm underline" href="{{ route('login') }}">
                        {{ __('Back to Sign in') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
