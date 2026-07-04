@props(['active' => ''])
@php
    $links = [
        'Homepage' => route('home'),
        'Catalog'  => route('catalog'),
        'Dosen'    => route('dosen.public.index'),
        'About us' => route('about'),
        'FAQ'      => route('faq'),
    ];
@endphp

<header class="bg-ink-900 sticky top-0 z-50 shadow-md" role="banner">
  <nav class="mx-auto flex h-[68px] max-w-[1440px] items-center gap-4 px-6 lg:px-8"
       aria-label="Navigasi utama">

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-2 text-white focus:outline-none focus:ring-2 focus:ring-white/50 rounded" aria-label="SI-Pedia — Beranda">
      <x-cap class="h-7 w-7" aria-hidden="true" />
      <span class="text-xl font-extrabold tracking-tight">SI-Pedia</span>
    </a>

    {{-- Desktop nav links --}}
    <div class="hidden flex-1 items-center gap-8 lg:flex" role="navigation">
      @foreach ($links as $label => $url)
        <a href="{{ $url }}"
           class="text-sm transition-colors {{ $active === $label ? 'font-bold text-white' : 'font-medium text-white/75 hover:text-white' }}"
           @if($active === $label) aria-current="page" @endif>
          {{ $label }}
        </a>
      @endforeach
    </div>

    {{-- Global Search --}}
    <div class="relative flex-1 lg:max-w-xs xl:max-w-sm" role="search">
      <label for="global-search" class="sr-only">Cari artikel, dosen, atau kategori</label>
      <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3" aria-hidden="true">
          <svg class="h-4 w-4 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
          </svg>
        </div>
        <input
          id="global-search"
          type="search"
          name="q"
          placeholder="Cari artikel, dosen..."
          autocomplete="off"
          aria-label="Cari konten"
          aria-controls="search-suggestions"
          aria-expanded="false"
          aria-haspopup="listbox"
          class="w-full rounded-lg bg-white/10 py-2 pl-9 pr-4 text-sm text-white placeholder:text-white/40 border border-transparent focus:border-white/30 focus:bg-white/15 focus:outline-none transition-all"
        >
        {{-- Suggestions dropdown --}}
        <div id="search-suggestions"
             role="listbox"
             aria-label="Saran pencarian"
             class="search-suggestions absolute left-0 top-full mt-1 hidden w-full min-w-[280px] overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700 z-50">
          <div id="search-suggestions-content" class="py-1"></div>
          <div id="search-no-results" class="hidden px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
            Tidak ada hasil untuk "<span id="search-no-results-q"></span>"
          </div>
          <div id="search-loading" class="hidden px-4 py-3 text-center">
            <div class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-brand-600 border-t-transparent" aria-label="Memuat..."></div>
          </div>
        </div>
      </div>
    </div>

    @auth
    {{-- User dropdown --}}
    <div class="relative" id="user-menu-container">
      <button id="user-menu-btn"
              aria-haspopup="true"
              aria-expanded="false"
              aria-controls="user-menu"
              aria-label="Menu profil {{ auth()->user()->name }}"
              class="flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-white hover:bg-white/20 transition focus:outline-none focus:ring-2 focus:ring-white/50">
        <img src="{{ auth()->user()->avatar_url }}"
             alt="Foto profil {{ auth()->user()->name }}"
             class="h-7 w-7 rounded-full object-cover"
             loading="lazy">
        <span class="hidden max-w-[120px] truncate text-sm font-semibold lg:block">{{ auth()->user()->name }}</span>
        <svg class="h-3.5 w-3.5 text-white/60 transition-transform" id="user-menu-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
        </svg>
      </button>

      <div id="user-menu"
           role="menu"
           aria-label="Menu pengguna"
           class="absolute right-0 top-full mt-2 hidden w-56 overflow-hidden rounded-xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-xl z-50">
        {{-- User info --}}
        <div class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3">
          <p class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
          <p class="truncate text-xs text-gray-400">{{ auth()->user()->email }}</p>
          <span class="mt-1 inline-block rounded-full bg-brand-600/10 px-2 py-0.5 text-xs font-bold text-brand-700">
            {{ ucfirst(auth()->user()->role) }}
          </span>
        </div>

        <div class="py-1" role="none">
          @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.panel') }}" role="menuitem" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:bg-gray-50 dark:focus:bg-gray-700 focus:outline-none">
              <span aria-hidden="true">🛠</span> Admin Panel
            </a>
            <a href="{{ route('admin.articles.pending') }}" role="menuitem" class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:bg-gray-50 dark:focus:bg-gray-700 focus:outline-none">
              <span class="flex items-center gap-2.5"><span aria-hidden="true">📋</span> Pending Artikel</span>
              @php $pc = \App\Models\Article::whereIn('status',['pending','pending_delete'])->count(); @endphp
              @if($pc > 0)<span class="rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white" aria-label="{{ $pc }} artikel pending">{{ $pc }}</span>@endif
            </a>
            <div class="my-1 border-t border-gray-100 dark:border-gray-700" role="separator"></div>
          @endif

          <a href="{{ route('articles.create') }}" role="menuitem" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-brand-700 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-gray-700 focus:bg-brand-50 dark:focus:bg-gray-700 focus:outline-none">
            <span aria-hidden="true">✏️</span> Tulis Artikel
          </a>
          <a href="{{ route('articles.my') }}" role="menuitem" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:bg-gray-50 dark:focus:bg-gray-700 focus:outline-none">
            <span aria-hidden="true">📄</span> Artikel Saya
          </a>
          <div class="my-1 border-t border-gray-100 dark:border-gray-700" role="separator"></div>
          <a href="{{ route('profile.show') }}" role="menuitem" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:bg-gray-50 dark:focus:bg-gray-700 focus:outline-none">
            <span aria-hidden="true">👤</span> Profil Saya
          </a>
          <form method="POST" action="{{ route('logout') }}" class="m-0" role="none">
            @csrf
            <button type="submit" role="menuitem" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 focus:bg-red-50 focus:outline-none">
              <span aria-hidden="true">🚪</span> Keluar
            </button>
          </form>
        </div>
      </div>
    </div>
    @else
    <div class="hidden items-center gap-3 lg:flex">
      <a href="{{ route('login') }}" class="text-sm font-semibold text-white/80 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-white/50 rounded px-2 py-1">Masuk</a>
      <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">Daftar</a>
    </div>
    @endauth


    {{-- Dark mode toggle --}}
    <button data-dark-toggle id="dark-toggle"
            class="flex h-9 w-9 items-center justify-center rounded-lg text-white/70 hover:bg-white/10 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-white/50"
            aria-label="Toggle dark mode">
      <svg id="dark-toggle-moon" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
      </svg>
      <svg id="dark-toggle-sun" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
      </svg>
    </button>

    {{-- Mobile hamburger --}}
    <button id="mobile-menu-btn"
            aria-expanded="false"
            aria-controls="mobile-menu"
            aria-label="Buka menu navigasi"
            class="ml-auto flex h-9 w-9 items-center justify-center rounded-lg text-white hover:bg-white/10 transition focus:outline-none focus:ring-2 focus:ring-white/50 lg:hidden">
      <svg id="hamburger-icon" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
      </svg>
      <svg id="close-icon" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </nav>

  {{-- Mobile menu --}}
  <div id="mobile-menu" class="hidden bg-ink-800 lg:hidden" role="navigation" aria-label="Navigasi mobile">
    <div class="mx-auto max-w-[1440px] space-y-1 px-6 pb-4">
      {{-- Mobile search --}}
      <div class="pt-3 pb-2">
        <form action="{{ route('search') }}" method="GET">
          <label for="mobile-search" class="sr-only">Cari konten</label>
          <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3" aria-hidden="true">
              <svg class="h-4 w-4 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
              </svg>
            </div>
            <input id="mobile-search" type="search" name="q" placeholder="Cari..." value="{{ request('q') }}"
                   class="w-full rounded-lg bg-white/10 py-2 pl-9 pr-4 text-sm text-white placeholder:text-white/40 border border-transparent focus:border-white/30 focus:outline-none">
          </div>
        </form>
      </div>

      @foreach ($links as $label => $url)
        <a href="{{ $url }}"
           class="block rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $active === $label ? 'bg-white/10 font-bold text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}"
           @if($active === $label) aria-current="page" @endif>
          {{ $label }}
        </a>
      @endforeach

      <div class="border-t border-white/10 pt-3 mt-2">
        @auth
          <a href="{{ route('profile.show') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-white hover:bg-white/10 transition">
            <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}" class="h-7 w-7 rounded-full object-cover">
            {{ auth()->user()->name }}
          </a>
          <a href="{{ route('articles.create') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-brand-300 hover:bg-white/10 transition">✏️ Tulis Artikel</a>
          <a href="{{ route('articles.my') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-white/75 hover:bg-white/10 transition">📄 Artikel Saya</a>
          @if(auth()->user()->role === 'admin')
          <a href="{{ route('admin.panel') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-white/75 hover:bg-white/10 transition">🛠 Admin Panel</a>
          @endif
          <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-400 hover:bg-white/10 transition">🚪 Keluar</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-white/75 hover:bg-white/10 transition">Masuk</a>
          <a href="{{ route('register') }}" class="mt-1 block rounded-lg bg-brand-600 px-3 py-2.5 text-center text-sm font-semibold text-white hover:bg-brand-700 transition">Daftar</a>
        @endauth
      </div>
    </div>
  </div>
</header>
