<x-auth-layout title="Register — SI-Pedia">
    <h1 class="mb-9 text-center text-4xl font-extrabold tracking-tight text-gray-900">SIGN UP</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   placeholder="Full Name"
                   class="h-[52px] w-full rounded-[14px] border-0 bg-field px-6 text-gray-800 placeholder-gray-600 shadow-sm focus:ring-2 focus:ring-brand-600">
            @error('name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="email" name="email" value="{{ old('email') }}" required
                   placeholder="Email@gmail.com"
                   class="h-[52px] w-full rounded-[14px] border-0 bg-field px-6 text-gray-800 placeholder-gray-600 shadow-sm focus:ring-2 focus:ring-brand-600">
            @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="password" name="password" required
                   placeholder="Password"
                   class="h-[52px] w-full rounded-[14px] border-0 bg-field px-6 text-gray-800 placeholder-gray-600 shadow-sm focus:ring-2 focus:ring-brand-600">
            @error('password') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div>
            <input type="password" name="password_confirmation" required
                   placeholder="Confirm Password"
                   class="h-[52px] w-full rounded-[14px] border-0 bg-field px-6 text-gray-800 placeholder-gray-600 shadow-sm focus:ring-2 focus:ring-brand-600">
        </div>

        <label class="flex items-center gap-3 text-[15px] text-gray-700">
            <input type="checkbox" name="terms" required
                   class="h-5 w-5 rounded border-0 bg-field text-brand-600 focus:ring-brand-600">
            I agree to the database terms
        </label>

        <button type="submit"
                class="h-[56px] w-full rounded-[10px] bg-brand-600 text-lg font-bold tracking-wide text-white shadow-md transition hover:bg-brand-700">
            REGISTER
        </button>
    </form>

    <div class="mt-7 flex items-center gap-4">
        <span class="h-px flex-1 bg-gray-300"></span>
        <span class="text-sm italic font-semibold text-gray-400">already have an acc?</span>
        <span class="h-px flex-1 bg-gray-300"></span>
    </div>
    <p class="mt-3 text-center">
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Login Now!</a>
    </p>
</x-auth-layout>
