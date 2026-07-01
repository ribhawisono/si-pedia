@props([
    'title'            => 'SI-Pedia',
    'active'           => '',
    'footer'           => 'none',
    'meta_description' => 'Platform digital ensiklopedia Program Studi Sistem Informasi Universitas Indraprasta PGRI.',
    'og_image'         => null,
    'canonical_url'    => null,
])
<!DOCTYPE html>
<script>if(localStorage.getItem("si-pedia-theme")==="dark"||(!localStorage.getItem("si-pedia-theme")&&window.matchMedia("(prefers-color-scheme: dark)").matches)){document.documentElement.classList.add("dark")}</script>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $meta_description }}">

    {{-- OpenGraph --}}
    <meta property="og:title"       content="{{ $title }}">
    <meta property="og:description" content="{{ $meta_description }}">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url()->current() }}">
    @if($og_image)
    <meta property="og:image"       content="{{ $og_image }}">
    <meta name="twitter:image"      content="{{ $og_image }}">
    @endif
    <meta name="twitter:card"       content="summary_large_image">
    <meta name="twitter:title"      content="{{ $title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <title>{{ $title }}</title>
    @if($canonical_url)
    <link rel="canonical" href="{{ $canonical_url }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-gray-900 antialiased" id="top">

    {{-- Skip to content (accessibility) --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[9999] focus:rounded-lg focus:bg-brand-600 focus:px-4 focus:py-2 focus:text-sm focus:font-bold focus:text-white focus:shadow-lg">
        Lewati ke konten utama
    </a>

    {{-- Navbar --}}
    <x-navbar :active="$active" />

    {{-- Flash messages --}}
    @if(session('success') || session('error') || session('status'))
    <div id="flash-container" class="fixed top-4 right-4 z-50 w-80 space-y-2" role="alert" aria-live="polite">
        @if(session('success'))
        <div class="flash-msg flex items-start gap-3 rounded-xl bg-green-600 px-4 py-3 text-sm font-semibold text-white shadow-lg">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            <span>{{ session('success') }}</span>
            <button onclick="this.closest('.flash-msg').remove()" class="ml-auto text-white/70 hover:text-white" aria-label="Tutup notifikasi">✕</button>
        </div>
        @endif
        @if(session('error'))
        <div class="flash-msg flex items-start gap-3 rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white shadow-lg">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
            </svg>
            <span>{{ session('error') }}</span>
            <button onclick="this.closest('.flash-msg').remove()" class="ml-auto text-white/70 hover:text-white" aria-label="Tutup notifikasi">✕</button>
        </div>
        @endif
        @if(session('status'))
        <div class="flash-msg flex items-start gap-3 rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
            <span>{{ session('status') }}</span>
            <button onclick="this.closest('.flash-msg').remove()" class="ml-auto text-white/70 hover:text-white" aria-label="Tutup notifikasi">✕</button>
        </div>
        @endif
    </div>
    @endif

    {{-- Main content --}}
    <main id="main-content" tabindex="-1">
        {{ $slot }}
    </main>

    {{-- Footers --}}
    @if ($footer === 'full')
        <x-footer />
    @elseif ($footer === 'min')
        <x-footer-min />
    @endif

    {{-- Back to top --}}
    <button id="back-to-top"
            onclick="window.scrollTo({top:0,behavior:'smooth'})"
            aria-label="Kembali ke atas"
            class="fixed bottom-6 right-6 z-40 hidden h-10 w-10 items-center justify-center rounded-full bg-brand-600 text-white shadow-lg hover:bg-brand-700 transition-all focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
        </svg>
    </button>

</body>
</html>
