@props([
    'title'   => 'Admin — SI-Pedia',
    'section' => '',
])
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased" id="top">

@php
$pendingArticles = \App\Models\Article::whereIn('status',['pending','pending_delete'])->count();
$pendingReports  = \App\Models\AccountReport::where('status','pending')->count();
$pendingComments = \App\Models\Comment::where('status','pending')->count();
$totalBadge = $pendingArticles + $pendingReports + $pendingComments;

$nav = [
    'dashboard' => ['label'=>'Dashboard',   'icon'=>'🏠', 'route'=>'admin.panel'],
    'articles'  => ['label'=>'Artikel',     'icon'=>'📄', 'route'=>'admin.articles.index',  'badge'=>$pendingArticles],
    'pending'   => ['label'=>'Pending',     'icon'=>'⏳', 'route'=>'admin.articles.pending','badge'=>$pendingArticles, 'sub'=>true],
    'comments'  => ['label'=>'Komentar',    'icon'=>'💬', 'route'=>'admin.comments.index',  'badge'=>$pendingComments],
    'categories'=> ['label'=>'Kategori',    'icon'=>'📂', 'route'=>'admin.categories.index'],
    'users'     => ['label'=>'Users',       'icon'=>'👥', 'route'=>'admin.users.index'],
    'dosen'     => ['label'=>'Dosen',       'icon'=>'🎓', 'route'=>'admin.dosen.index'],
    'reports'   => ['label'=>'Report Akun', 'icon'=>'🚩', 'route'=>'admin.account-reports.index', 'badge'=>$pendingReports],
    'analytics' => ['label'=>'Laporan',     'icon'=>'📊', 'route'=>'admin.report'],
];
@endphp

<div class="flex min-h-screen">

    {{-- ── SIDEBAR ───────────────────────────────────────────────────────── --}}
    <aside id="admin-sidebar"
           class="flex w-64 flex-shrink-0 flex-col bg-ink-900 transition-all duration-300 lg:relative fixed inset-y-0 left-0 z-50 -translate-x-full lg:translate-x-0"
           aria-label="Navigasi admin">

        {{-- Logo --}}
        <div class="flex h-16 items-center justify-between px-5 border-b border-white/10">
            <a href="{{ route('admin.panel') }}" class="flex items-center gap-2.5 text-white focus:outline-none focus:ring-2 focus:ring-white/50 rounded">
                <x-cap class="h-7 w-7" aria-hidden="true"/>
                <span class="font-extrabold text-lg tracking-tight">SI-Pedia</span>
                <span class="rounded-full bg-brand-600 px-1.5 py-0.5 text-[10px] font-bold text-white">Admin</span>
            </a>
            <button id="sidebar-close" class="lg:hidden text-white/60 hover:text-white p-1 focus:outline-none" aria-label="Tutup sidebar">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3" aria-label="Menu admin">
            {{-- Konten --}}
            <p class="mb-1 px-3 text-[10px] font-bold uppercase tracking-widest text-white/30">Konten</p>
            @foreach(['dashboard','articles','pending','comments','categories'] as $key)
            @php $item = $nav[$key]; @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors mb-0.5
                      {{ $section === $key ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:bg-white/10 hover:text-white' }}
                      {{ ($item['sub'] ?? false) ? 'ml-4' : '' }}"
               @if($section === $key) aria-current="page" @endif>
                <span aria-hidden="true" class="text-base">{{ $item['icon'] }}</span>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if(!empty($item['badge']) && $item['badge'] > 0)
                <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-black text-white" aria-label="{{ $item['badge'] }} item menunggu">
                    {{ $item['badge'] }}
                </span>
                @endif
            </a>
            @endforeach

            {{-- Pengguna --}}
            <p class="mb-1 mt-4 px-3 text-[10px] font-bold uppercase tracking-widest text-white/30">Pengguna</p>
            @foreach(['users','dosen'] as $key)
            @php $item = $nav[$key]; @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors mb-0.5
                      {{ $section === $key ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
               @if($section === $key) aria-current="page" @endif>
                <span aria-hidden="true" class="text-base">{{ $item['icon'] }}</span>
                <span class="flex-1">{{ $item['label'] }}</span>
            </a>
            @endforeach

            {{-- Laporan --}}
            <p class="mb-1 mt-4 px-3 text-[10px] font-bold uppercase tracking-widest text-white/30">Laporan</p>
            @foreach(['reports','analytics'] as $key)
            @php $item = $nav[$key]; @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors mb-0.5
                      {{ $section === $key ? 'bg-white/15 text-white font-semibold' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
               @if($section === $key) aria-current="page" @endif>
                <span aria-hidden="true" class="text-base">{{ $item['icon'] }}</span>
                <span class="flex-1">{{ $item['label'] }}</span>
                @if(!empty($item['badge']) && $item['badge'] > 0)
                <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-black text-white">{{ $item['badge'] }}</span>
                @endif
            </a>
            @endforeach
        </nav>

        {{-- User info at bottom --}}
        <div class="border-t border-white/10 p-4">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}" alt="Foto {{ auth()->user()->name }}" class="h-8 w-8 rounded-full object-cover flex-shrink-0">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[10px] text-white/50">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('home') }}" class="flex-1 rounded-lg border border-white/10 py-1.5 text-center text-xs font-semibold text-white/70 hover:bg-white/10 transition">
                    ← Web
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-red-500/30 py-1.5 text-center text-xs font-semibold text-red-400 hover:bg-red-500/10 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Sidebar overlay (mobile) --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden" aria-hidden="true"></div>

    {{-- ── MAIN AREA ─────────────────────────────────────────────────────── --}}
    <div class="flex min-w-0 flex-1 flex-col">

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between bg-white border-b border-gray-200 px-4 sm:px-6 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Mobile sidebar toggle --}}
                <button id="sidebar-open" aria-label="Buka menu navigasi admin" aria-controls="admin-sidebar" aria-expanded="false"
                        class="rounded-lg p-2 text-gray-600 hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-brand-600 lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
                <h1 class="text-base font-bold text-gray-900 truncate">{{ $title }}</h1>
            </div>

            {{-- Top bar right --}}
            <div class="flex items-center gap-3">
                {{-- Global notification badge --}}
                @if($totalBadge > 0)
                <a href="{{ route('admin.articles.pending') }}"
                   class="relative rounded-lg p-2 text-gray-600 hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-brand-600"
                   aria-label="{{ $totalBadge }} item memerlukan perhatian">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-black text-white">
                        {{ $totalBadge }}
                    </span>
                </a>
                @endif

            {{-- Dark mode toggle --}}
            <button data-dark-toggle
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 transition focus:outline-none focus:ring-2 focus:ring-brand-600"
                    aria-label="Toggle dark mode">
              <svg class="dark-toggle-moon h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
              </svg>
              <svg class="dark-toggle-sun hidden h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
              </svg>
            </button>
                {{-- Add article quick action --}}
                <a href="{{ route('admin.articles.create') }}"
                   class="hidden items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-xs font-bold text-white hover:bg-brand-700 transition sm:flex focus:outline-none focus:ring-2 focus:ring-brand-400">
                    + Artikel Baru
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success') || session('error'))
        <div class="px-4 pt-4 sm:px-6" role="alert" aria-live="polite">
            @if(session('success'))
            <div class="flex items-center gap-2 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm font-semibold text-green-700 mb-2">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-2 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm font-semibold text-red-700 mb-2">
                <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                {{ session('error') }}
            </div>
            @endif
        </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-4 sm:p-6" id="main-content" tabindex="-1">
            {{ $slot }}
        </main>
    </div>
</div>

<script>
// Admin sidebar toggle (mobile)
(function() {
    const open    = document.getElementById('sidebar-open');
    const close   = document.getElementById('sidebar-close');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        open?.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        open?.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    open?.addEventListener('click', openSidebar);
    close?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });
})();
</script>

</body>
</html>
