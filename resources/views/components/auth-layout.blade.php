{{-- Layout khusus halaman autentikasi: panel navy kiri + slot form di kanan --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SI-Pedia' }}</title>

    {{-- Favicon (logo topi wisuda SI-Pedia) --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Crect width='24' height='24' rx='5' fill='%230a0f2c'/%3E%3Cpath fill='white' d='M12 3 1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zM18.18 12 12 15.72 5.82 12 12 8.28 18.18 12zM7 14.5l5 2.72 5-2.72v2.41L12 19.5l-5-2.59V14.5z'/%3E%3C/svg%3E">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans">
    <div class="flex min-h-screen">

        {{-- LEFT: hero panel --}}
        <div class="relative hidden md:flex w-[43%] flex-col items-center justify-center rounded-r-[40px] bg-ink-900 px-10 text-center shadow-2xl">
            <img src="{{ asset('images/auth-illustration.png') }}" alt="Encyclopedia Information System"
                 class="w-[400px] max-w-full">
            <h1 class="mt-2 text-5xl font-extrabold leading-[1.1] text-white">ENCYCLOPEDIA</h1>
            <h2 class="text-5xl font-extrabold leading-[1.1] text-[#c5c8da]">Information System</h2>
            <p class="mt-5 text-lg text-[#9aa0b5]">Explore the digital database.</p>
        </div>

        {{-- RIGHT: form --}}
        <div class="flex flex-1 items-center justify-center px-8 py-12">
            <div class="w-full max-w-[460px]">
                {{ $slot }}
            </div>
        </div>

    </div>
</body>
</html>
