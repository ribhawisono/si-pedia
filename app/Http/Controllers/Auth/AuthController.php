<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function showLogin()  { return view('auth.login'); }
    public function showForgot() { return view('auth.forgot-password'); }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        $credentials['email'] = strtolower($credentials['email']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request)
    {
        $request->merge(['email' => strtolower((string) $request->input('email'))]);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'terms'    => 'accepted',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'user',
        ]);

        // Kirim OTP
        $this->sendOtp($user);

        Auth::login($user);

        return redirect()->route('verification.otp')->with('status', 'Kode verifikasi telah dikirim ke email kamu.');
    }

    // OTP Verification

    private function sendOtp(User $user): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerificationCode::where('user_id', $user->id)->delete();
        EmailVerificationCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Dev fallback (mailer=log) tetap sync, ringan & tanpa I/O jaringan
        if (config('mail.default') === 'log') {
            session(['dev_otp' => $code]);
        }

        // Kirim email async setelah response dikirim, supaya SMTP lambat/hang
        // tidak memblokir seluruh request (dan seluruh site di single-process server)
        defer(function () use ($user, $code) {
            try {
                Mail::send([], [], function ($message) use ($user, $code) {
                    $message->to($user->email, $user->name)
                        ->subject('Kode Verifikasi SI-Pedia')
                        ->html("
                            <div style='font-family:sans-serif;max-width:480px;margin:auto;padding:32px;border:1px solid #e5e7eb;border-radius:12px'>
                                <h2 style='color:#0a0b2f;margin-bottom:8px'>Verifikasi Email Kamu</h2>
                                <p style='color:#6b7280;margin-bottom:24px'>Masukkan kode berikut di halaman verifikasi SI-Pedia. Kode berlaku <strong>10 menit</strong>.</p>
                                <div style='background:#f3f4f6;border-radius:8px;padding:24px;text-align:center'>
                                    <span style='font-size:40px;font-weight:900;letter-spacing:12px;color:#336cbc'>{$code}</span>
                                </div>
                                <p style='color:#9ca3af;font-size:12px;margin-top:24px'>Jika kamu tidak mendaftar di SI-Pedia, abaikan email ini.</p>
                            </div>
                        ");
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('OTP mail failed: ' . $e->getMessage());
            }
        });
    }

    public function showOtp()
    {
        if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = auth()->user();

        $record = EmailVerificationCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->first();

        if (!$record || $record->isExpired()) {
            return back()->withErrors(['code' => 'Kode tidak valid atau sudah kadaluarsa.']);
        }

        $user->markEmailAsVerified();
        $record->delete();

        return redirect()->route('home')->with('status', 'Email berhasil diverifikasi! Selamat datang di SI-Pedia.');
    }

    public function resendOtp()
    {
        $user = auth()->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        $this->sendOtp($user);

        return back()->with('status', 'Kode verifikasi baru telah dikirim ke ' . $user->email);
    }

    // Reset Password

    public function sendResetLink(Request $request)
    {
        $request->merge(['email' => strtolower((string) $request->input('email'))]);
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(string $token) { return view('auth.reset-password', ['token' => $token]); }

    public function resetPassword(Request $request)
    {
        $request->merge(['email' => strtolower((string) $request->input('email'))]);

        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            fn ($user, $password) => $user->forceFill(['password' => Hash::make($password)])->save()
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
