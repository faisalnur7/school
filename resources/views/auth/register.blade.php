<x-guest-layout>
    <div class="w-full flex mx-auto h-dvh">
        <div class="w-1/2 hidden lg:flex bg-[url('./assets/img/backgrounds/sign_in_bg.png')] bg-no-repeat bg-cover bg-bottom"></div>
        <div class="w-full lg:w-1/2 bg-white p-10 lg:p-12 flex flex-col gap-4 justify-start lg:justify-center">
            <div class="flex flex-col gap-2 items-start">
                <a href="{{route('homepage')}}" class="p-3 flex lg:hidden text-white rounded-lg ml-0 border-2 border-gray-200 bg-gray-800">Go to Home </a>
                <h1 class="text-4xl font-bold mb-3">Welcome to our store</h1>
            </div>
            <div>
                <form method="POST" action="{{ route('customer.register') }}">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Phone -->
                    <div class="mt-6">
                        <x-input-label for="phone" :value="__('Phone')" />
                        <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required autofocus autocomplete="phone" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <!-- Email Address -->
                    <div class="mt-6">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-6">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="mt-6">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                        type="password"
                                        name="password_confirmation" required autocomplete="new-password" />

                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-start justify-end mt-6 flex-col w-full gap-1">
                        <p class="text-gray-500 ">Already have an account? <a class="underline decoration-1" href="{{route('login')}}">Sign in</a></p>
                        <p class="text-gray-500 ">By creating the account, you agree to the <a href="#">Terms of use</a> and  <a href="#">Privacy Policy</a></p>

                        <x-primary-button class="w-full lg:w-1/2 flex mx-auto justify-center align-center py-3 mt-6 capitalize !text-lg !rounded-full">
                            {{ __('Create an Account') }}
                        </x-primary-button>


                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
