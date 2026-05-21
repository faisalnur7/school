<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8">
            <div class="flex flex-col gap-4 mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-800">Enter Verification Code</h1>
                <p class="text-gray-500">Enter the 6-digit code sent to your email</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            @if($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 text-red-700 px-3 py-2 text-sm">
                    {{ $errors->first('code') ?: $errors->first() }}
                </div>
            @endif

            <form id="verifyForm" method="POST" action="{{ route('login.verify.submit') }}" class="flex flex-col gap-5">
                @csrf
                <input type="hidden" name="code" id="fullCode" value="{{ old('code') }}">

                <div class="flex justify-center gap-2">
                    @for($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="1"
                            class="code-input w-12 h-12 text-center text-xl font-bold border border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500"
                            value="{{ substr(old('code', ''), $i, 1) }}"
                            autocomplete="one-time-code"
                        >
                    @endfor
                </div>

                <x-primary-button class="w-full py-3 rounded-full flex justify-center items-center bg-gradient-to-r from-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-purple-600 text-white font-semibold transition">
                    Verify & Continue
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('login.verify.resend') }}" class="mt-3 text-center">
                @csrf
                <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-sm underline">Resend Code</button>
            </form>

            <div class="mt-3 text-center">
                <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('verifyForm');
            const fullCode = document.getElementById('fullCode');
            const inputs = Array.from(document.querySelectorAll('.code-input'));

            const syncCode = () => {
                fullCode.value = inputs.map(input => input.value.replace(/\D/g, '')).join('');
            };

            const submitIfComplete = () => {
                syncCode();
                if (fullCode.value.length === 6) {
                    form.submit();
                }
            };

            inputs.forEach((input, index) => {
                input.addEventListener('input', () => {
                    input.value = input.value.replace(/\D/g, '').slice(0, 1);
                    if (input.value && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    submitIfComplete();
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !input.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    if (!pasted) return;

                    e.preventDefault();

                    pasted.split('').forEach((digit, idx) => {
                        if (inputs[idx]) inputs[idx].value = digit;
                    });

                    const nextIndex = Math.min(pasted.length, 5);
                    inputs[nextIndex].focus();
                    submitIfComplete();
                });
            });

            const existing = fullCode.value.replace(/\D/g, '').slice(0, 6);
            if (existing) {
                existing.split('').forEach((digit, idx) => {
                    if (inputs[idx]) inputs[idx].value = digit;
                });
            }

            inputs[0].focus();
        });
    </script>
</x-guest-layout>
