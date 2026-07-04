<x-auth-layout title="Verify Email — SI-Pedia">
    <h1 class="mb-2 text-center text-3xl font-extrabold tracking-tight text-gray-900">Verify Your Email</h1>
    <p class="mb-6 text-center text-gray-500">
        Thanks for signing up! Before getting started, please verify your email address by clicking the link we just sent.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm font-medium">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit"
            class="h-[52px] w-full rounded-[14px] bg-brand-600 text-white font-semibold shadow-sm transition hover:bg-brand-700">
            Resend Verification Email
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-gray-700 underline">
            Log Out
        </button>
    </form>
</x-auth-layout>
