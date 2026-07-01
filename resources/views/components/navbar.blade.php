@props(['active' => ''])
@php
    $links = [
        'Homepage' => route('home'),
        'Catalog'  => route('catalog'),
        'About us' => route('about'),
        'Review'   => route('review.index'),
        'FAQ'      => route('faq'),
    ];
@endphp
<header class="bg-ink-900 relative z-50">
    <nav class="mx-auto flex h-[72px] max-w-[1440px] items-center justify-between px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-white">
            <x-cap class="h-7 w-7" />
            <span class="text-xl font-extrabold tracking-tight">SI-Pedia</span>
        </a>

        <div class="hidden items-center gap-10 md:flex">
            @foreach ($links as $label => $url)
                <a href="{{ $url }}"
                   class="text-sm {{ $active === $label ? 'font-bold text-white' : 'font-medium text-white/80 hover:text-white' }} transition-colors">
                   {{ $label }}
                </a>
            @endforeach
        </div>

        @auth
        <div class="flex items-center gap-3">
            <div class="relative group">
                {{-- Avatar trigger --}}
                <button class="flex items-center gap-2 rounded-full bg-white/10 hover:bg-white/20 px-3 py-1.5 transition">
                    <div class="h-7 w-7 rounded-full overflow-hidden flex-shrink-0">
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-full h-full object-cover">
                    </div>
                    <span class="text-sm font-semibold text-white max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                    <svg class="h-3.5 w-3.5 text-white/60" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
                    </svg>
                </button>

                {{-- Dropdown --}}
                <div class="absolute right-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                    <div class="bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden w-52">

                        {{-- User info header --}}
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-1 rounded-full bg-brand-600/10 px-2 py-0.5 text-xs font-bold text-brand-700">
                                {{ ucfirst(auth()->user()->role) }}
                            </span>
                        </div>

                        {{-- Menu items --}}
                        <div class="py-1">
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.panel') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                    <span class="text-base">🛠</span> Admin Panel
                                </a>
                                <a href="{{ route('admin.articles.pending') }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                    <span class="flex items-center gap-2.5"><span class="text-base">📋</span> Pending Artikel</span>
                                    @php $pc = \App\Models\Article::whereIn('status',['pending','pending_delete'])->count(); @endphp
                                    @if($pc > 0)<span class="rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">{{ $pc }}</span>@endif
                                </a>
                                <a href="{{ route('admin.account-reports.index') }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                    <span class="flex items-center gap-2.5"><span class="text-base">🚩</span> Report Akun</span>
                                    @php $rc = \App\Models\AccountReport::where('status','pending')->count(); @endphp
                                    @if($rc > 0)<span class="rounded-full bg-red-500 px-1.5 py-0.5 text-xs font-bold text-white">{{ $rc }}</span>@endif
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                            @endif

                            {{-- Semua user: Tulis Artikel & Artikel Saya --}}
                            <a href="{{ route('articles.create') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                                <span class="text-base">✏️</span> Tulis Artikel
                            </a>
                            <a href="{{ route('articles.my') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                <span class="text-base">📄</span> Artikel Saya
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                <span class="text-base">👤</span> Profil Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                    <span class="text-base">🚪</span> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="text-sm font-semibold text-white/80 hover:text-white transition">Masuk</a>
            <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition">Daftar</a>
        </div>
        @endauth
    </nav>
</header>
