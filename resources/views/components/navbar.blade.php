@props(['active' => ''])
@php
    $links = [
        'Homepage' => route('home'),
        'Catalog'  => route('catalog'),
        'About us' => route('about'),
        'Review'   => route('review.index'),
        'FAQ'     => route('faq'),
    ];
@endphp
<header class="bg-ink-900 relative z-50">
    <nav class="mx-auto flex h-[80px] max-w-[1440px] items-center justify-between px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-white">
            <x-cap class="h-8 w-8" />
            <span class="text-2xl font-extrabold">SI-Pedia</span>
        </a>

        <div class="hidden items-center gap-12 md:flex">
            @foreach ($links as $label => $url)
                <a href="{{ $url }}"
                   class="text-[17px] {{ $active === $label ? 'font-semibold text-white' : 'font-medium text-white/90 hover:text-white' }}">{{ $label }}</a>
            @endforeach
        </div>

        @auth
        <div class="flex items-center gap-4">
            {{-- Tombol tulis artikel untuk semua user login --}}
            <a href="{{ route('articles.create') }}"
               class="rounded bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition flex items-center gap-1.5">
                ✏️ Tulis Artikel
            </a>

            <div class="relative group">
                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 text-[17px] font-bold text-white">
                    <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center overflow-hidden">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-sm font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        @endif
                    </div>
                    {{ auth()->user()->name }}
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 9l6 6 6-6" stroke-linecap="round"/></svg>
                </a>
                <div class="absolute right-0 top-full pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                    <div class="bg-white rounded shadow-lg overflow-hidden flex flex-col w-48">
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.panel') }}" class="px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-100 font-semibold">🛠 Admin Panel</a>
                            <a href="{{ route('admin.articles.pending') }}" class="px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-100 flex items-center justify-between">
                                📋 Pending Artikel
                                @php $pc = \App\Models\Article::whereIn('status', ['pending','pending_delete'])->count(); @endphp
                                @if($pc > 0)
                                    <span class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-bold text-white">{{ $pc }}</span>
                                @endif
                            </a>
                        @else
                            <a href="{{ route('articles.my') }}" class="px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-100">📄 Artikel Saya</a>
                        @endif
                        <a href="{{ route('profile.show') }}" class="px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-100">👤 Profile</a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-gray-100">🚪 Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="text-[17px] font-semibold text-white/90 hover:text-white">Login</a>
            <a href="{{ route('register') }}" class="rounded bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition">Register</a>
        </div>
        @endauth
    </nav>
</header>
