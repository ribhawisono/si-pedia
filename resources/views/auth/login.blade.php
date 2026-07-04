<x-auth-layout title="Login — SI-Pedia">
    <h1 class="mb-10 text-center text-4xl font-extrabold tracking-tight text-gray-900">SIGN IN</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="relative">
            <span class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-gray-500">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.4 0-8 2.7-8 6v2h16v-2c0-3.3-3.6-6-8-6z"/></svg>
            </span>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="Email@gmail.com"
                   class="h-[58px] w-full rounded-[14px] border-0 bg-field pl-16 pr-6 text-gray-800 placeholder-gray-600 shadow-md focus:ring-2 focus:ring-brand-600">
        </div>
        @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror

        {{-- Password --}}
        <div class="relative mt-7" x-data="{ show: false }">
            <span class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-gray-500">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M17 9V7a5 5 0 00-10 0v2H5v12h14V9h-2zM9 7a3 3 0 016 0v2H9V7z"/></svg>
            </span>
            <input type="password" name="password" required
                   placeholder="Password"
                   class="h-[58px] w-full rounded-[14px] border-0 bg-field pl-16 pr-14 text-gray-800 placeholder-gray-600 shadow-md focus:ring-2 focus:ring-brand-600">
            <span class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-500">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 13c3-4 7-5 10-5s7 1 10 5" stroke-linecap="round"/></svg>
            </span>
        </div>

        <div class="mt-5 flex items-center justify-between">
            <label class="flex items-center gap-3 text-[15px] text-gray-700">
                <input type="checkbox" name="remember"
                       class="h-5 w-5 rounded border-0 bg-field text-brand-600 focus:ring-brand-600">
                Remember
            </label>
            <a href="{{ route('password.request') }}" class="text-[15px] font-medium text-brand-600 hover:underline">forget password?</a>
        </div>

        <button type="submit"
                class="mt-7 h-[58px] w-full rounded-[12px] bg-brand-600 text-lg font-bold tracking-wide text-white shadow-[0_8px_16px_-4px_rgba(51,108,188,.6)] transition hover:bg-brand-700">
            ACCESS
        </button>
    </form>

    <div class="mt-8 flex items-center gap-4">
        <span class="h-px flex-1 bg-gray-300"></span>
        <span class="text-sm italic font-semibold text-gray-400">dont have an acc?</span>
        <span class="h-px flex-1 bg-gray-300"></span>
    </div>
    <p class="mt-3 text-center">
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">Register now!</a>
    </p>
</x-auth-layout>
