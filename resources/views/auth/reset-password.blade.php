<x-auth-layout title="Reset Password — SI-Pedia">
    <h1 class="mb-2 text-center text-3xl font-extrabold tracking-tight text-gray-900">Reset Password</h1>
    <p class="mb-6 text-center text-gray-500">Enter your new password below.</p>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                class="h-[52px] w-full rounded-[14px] border-0 bg-field px-6 text-gray-800 shadow-sm focus:ring-2 focus:ring-brand-600">
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="password" id="password" name="password" required
                class="h-[52px] w-full rounded-[14px] border-0 bg-field px-6 text-gray-800 shadow-sm focus:ring-2 focus:ring-brand-600">
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="h-[52px] w-full rounded-[14px] border-0 bg-field px-6 text-gray-800 shadow-sm focus:ring-2 focus:ring-brand-600">
        </div>

        <button type="submit"
            class="h-[56px] w-full rounded-[10px] bg-brand-600 text-lg font-bold tracking-wide text-white shadow-md transition hover:bg-brand-700">
            Reset Password
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Back to Login</a>
    </p>
</x-auth-layout>
