<x-auth-layout title="Verifikasi Email — SI-Pedia">
    <h2 class="text-2xl font-extrabold text-ink-900 mb-2">Verifikasi Email Kamu</h2>
    <p class="text-gray-500 text-sm mb-6">
        Kami sudah mengirim kode 6 digit ke <strong>{{ auth()->user()->email }}</strong>. Masukkan kode tersebut di bawah. Berlaku 10 menit.
    </p>

    @if(session('status'))
        <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm font-medium">
            ✅ {{ session('status') }}
        </div>
    @endif

    {{-- Dev mode: tampilkan OTP jika mail tidak terkonfigurasi --}}
    @if(session('dev_otp'))
        <div class="mb-4 p-3 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
            <p class="font-bold">⚠️ Mode Development</p>
            <p class="mt-1">Mail belum dikonfigurasi. Kode OTP kamu: <span class="font-mono font-black text-lg tracking-widest">{{ session('dev_otp') }}</span></p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.otp.verify') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Kode Verifikasi</label>
            <input type="text" name="code" maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                   placeholder="_ _ _ _ _ _"
                   class="w-full rounded-xl border-2 border-gray-200 px-5 py-4 text-center text-3xl font-black tracking-[0.5em] text-ink-900 focus:border-brand-600 focus:ring-0"
                   autofocus>
        </div>

        <button type="submit"
                class="w-full rounded-xl bg-brand-600 py-3 text-sm font-bold text-white hover:bg-brand-700 transition">
            Verifikasi Sekarang
        </button>
    </form>

    <div class="mt-5 text-center space-y-3">
        <p class="text-sm text-gray-500">Tidak menerima kode?</p>
        <form method="POST" action="{{ route('verification.otp.resend') }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                Kirim ulang kode →
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="w-full text-center text-xs text-gray-400 hover:text-gray-600 underline">
            Gunakan akun lain (Log Out)
        </button>
    </form>
</x-auth-layout>
