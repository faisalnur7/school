<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginVerificationCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginVerificationController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login_verification.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.login-verification-code');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $pendingUserId = $request->session()->get('login_verification.user_id');
        $remember = (bool) $request->session()->get('login_verification.remember', false);

        if (! $pendingUserId) {
            throw ValidationException::withMessages([
                'code' => 'Your verification session has expired. Please login again.',
            ]);
        }

        $user = User::find($pendingUserId);

        if (! $user || ! $user->login_verification_enabled) {
            $request->session()->forget('login_verification');

            throw ValidationException::withMessages([
                'code' => 'Your verification session is invalid. Please login again.',
            ]);
        }

        if (! $user->login_verification_code || ! $user->login_verification_expires_at || now()->gt($user->login_verification_expires_at)) {
            throw ValidationException::withMessages([
                'code' => 'Verification code has expired. Please request a new code.',
            ]);
        }

        if (! Hash::check($request->string('code')->toString(), $user->login_verification_code)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid verification code.',
            ]);
        }

        $user->forceFill([
            'login_verification_code' => null,
            'login_verification_expires_at' => null,
        ])->save();

        $request->session()->forget('login_verification');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function resend(Request $request): RedirectResponse
    {
        $pendingUserId = $request->session()->get('login_verification.user_id');

        if (! $pendingUserId) {
            return redirect()->route('login');
        }

        $user = User::find($pendingUserId);

        if (! $user || ! $user->login_verification_enabled) {
            $request->session()->forget('login_verification');

            return redirect()->route('login');
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'login_verification_code' => Hash::make($code),
            'login_verification_expires_at' => now()->addMinutes(10),
        ])->save();

        Mail::to($user->email)->send(new LoginVerificationCodeMail($user, $code));

        return back()->with('status', 'A new verification code has been sent to your email.');
    }
}
