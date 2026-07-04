<x-layouts.app title="SI-Pedia — Ensiklopedia Sistem Informasi" active="Homepage" footer="full">

{{-- ============================================ --}}
{{-- HERO SECTION                                 --}}
{{-- ============================================ --}}
<section class="relative overflow-hidden bg-ink-900">
    {{-- Decorative elements --}}
    <div class="absolute top-0 right-0 w-[600px] h-[600px] rounded-full bg-brand-600/10 blur-3xl -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] rounded-full bg-brand-600/5 blur-3xl translate-y-1/2 -translate-x-1/3"></div>

    <div class="relative mx-auto max-w-[1440px] px-8 py-20 lg:py-28">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            {{-- Left: Text --}}
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-600/15 px-4 py-1.5 text-sm font-semibold text-brand-400 mb-6">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                    Universitas Indraprasta PGRI
                </span>

                <h1 class="text-4xl font-black leading-[1.1] tracking-tight text-white sm:text-5xl lg:text-[3.5rem]">
                    {{ $page->title ?? 'Sistem Informasi Pedia' }}
                </h1>

                <p class="mt-6 max-w-lg text-lg leading-relaxed text-white/70">
                    {{ $page->content ?? 'Platform digital untuk menjelajahi artikel, informasi dosen, dan sumber daya akademik Program Studi Sistem Informasi.' }}
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 hover:bg-brand-700 transition-all hover:shadow-xl hover:shadow-brand-600/30 hover:-translate-y-0.5">
                        Jelajahi Artikel
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="{{ route('about') }}" class="inline-flex items-center gap-2 rounded-lg border border-white/20 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10 transition-all">
                        Tentang Prodi
                    </a>
                </div>
            </div>

            {{-- Right: Stats --}}
            <div class="hidden lg:block">
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-6 text-center">
                        <div class="text-4xl font-extrabold text-white">{{ \Illuminate\Support\Facades\Cache::remember('stat_articles', 300, fn() => \App\Models\Article::where('status','active')->count()) }}</div>
                        <div class="mt-2 text-sm font-medium text-white/60">Artikel</div>
                    </div>
                    <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-6 text-center">
                        <div class="text-4xl font-extrabold text-white">{{ \Illuminate\Support\Facades\Cache::remember('stat_lecturers', 300, fn() => \App\Models\Lecturer::count()) }}</div>
                        <div class="mt-2 text-sm font-medium text-white/60">Dosen</div>
                    </div>
                    <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-6 text-center">
                        <div class="text-4xl font-extrabold text-white">{{ \Illuminate\Support\Facades\Cache::remember('stat_categories', 300, fn() => \App\Models\Category::count()) }}</div>
                        <div class="mt-2 text-sm font-medium text-white/60">Kategori</div>
                    </div>
                    <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-6 text-center">
                        <div class="text-4xl font-extrabold text-white">{{ \Illuminate\Support\Facades\Cache::remember('stat_lecturers', 300, fn() => \App\Models\Lecturer::count()) > 0 ? '2023' : '-' }}</div>
                        <div class="mt-2 text-sm font-medium text-white/60">Berdiri</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- MOBILE STATS                                 --}}
{{-- ============================================ --}}
<section class="bg-ink-900 lg:hidden">
    <div class="mx-auto max-w-[1440px] px-8 pb-12">
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-5 text-center">
                <div class="text-3xl font-extrabold text-white">{{ \Illuminate\Support\Facades\Cache::remember('stat_articles', 300, fn() => \App\Models\Article::where('status','active')->count()) }}</div>
                <div class="mt-1 text-xs font-medium text-white/60">Artikel</div>
            </div>
            <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-5 text-center">
                <div class="text-3xl font-extrabold text-white">{{ \Illuminate\Support\Facades\Cache::remember('stat_lecturers', 300, fn() => \App\Models\Lecturer::count()) }}</div>
                <div class="mt-1 text-xs font-medium text-white/60">Dosen</div>
            </div>
            <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-5 text-center">
                <div class="text-3xl font-extrabold text-white">{{ \Illuminate\Support\Facades\Cache::remember('stat_categories', 300, fn() => \App\Models\Category::count()) }}</div>
                <div class="mt-1 text-xs font-medium text-white/60">Kategori</div>
            </div>
            <div class="rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-5 text-center">
                <div class="text-3xl font-extrabold text-white">{{ \Illuminate\Support\Facades\Cache::remember('stat_lecturers', 300, fn() => \App\Models\Lecturer::count()) > 0 ? '2023' : '-' }}</div>
                <div class="mt-1 text-xs font-medium text-white/60">Berdiri</div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- ARTIKEL TERBARU                              --}}
{{-- ============================================ --}}
<section class="bg-white py-20">
    <div class="mx-auto max-w-[1440px] px-8">

        {{-- Section Header --}}
        <div class="mb-12 flex items-end justify-between">
            <div>
                <span class="inline-block rounded-full bg-brand-600/10 px-4 py-1.5 text-sm font-semibold text-brand-600 dark:text-brand-400 tracking-wide mb-3">BERITA & ARTIKEL</span>
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight sm:text-4xl">Artikel Terbaru</h2>
            </div>
            <a href="{{ route('catalog') }}" class="hidden items-center gap-2 text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 transition sm:inline-flex">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        {{-- Articles Grid --}}
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($articles as $article)
            <a href="{{ route('articles.show', $article->slug ?? $article->id) }}" class="group block overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                {{-- Image --}}
                <div class="relative h-48 overflow-hidden bg-gray-100">
                    @if($article->image)
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Category Badge --}}
                    <div class="absolute top-3 left-3">
                        <span class="rounded-md bg-brand-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm">
                            {{ $article->category->name ?? 'Umum' }}
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-5">
                    <h3 class="text-lg font-bold leading-snug text-gray-900 dark:text-white line-clamp-2 group-hover:text-brand-700 transition-colors">
                        {{ $article->title }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 line-clamp-2">
                        {{ Str::limit(strip_tags($article->content), 120) }}
                    </p>
                    <div class="mt-4 flex items-center gap-2 text-sm font-semibold text-brand-600 dark:text-brand-400">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full py-16 text-center">
                <svg class="mx-auto w-16 h-16 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9.75m3 0l-3-3m3 3l-3 3M3.375 7.5h7.5c.621 0 1.125.504 1.125 1.125v7.5c0 .621-.504 1.125-1.125 1.125h-7.5A1.125 1.125 0 012.25 16.125v-7.5C2.25 8.004 2.754 7.5 3.375 7.5z"/>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-400">Belum ada artikel</h3>
                <p class="mt-1 text-sm text-gray-400">Artikel akan segera tersedia</p>
            </div>
            @endforelse
        </div>

        {{-- Mobile "Lihat Semua" --}}
        <div class="mt-8 text-center sm:hidden">
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-700 transition">
                Lihat Semua Artikel
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- KATEGORI                                     --}}
{{-- ============================================ --}}
<section class="bg-gray-50 border-t border-gray-100 py-16">
    <div class="mx-auto max-w-[1440px] px-8">
        <div class="mb-10 text-center">
            <span class="inline-block rounded-full bg-brand-600/10 px-4 py-1.5 text-sm font-semibold text-brand-600 dark:text-brand-400 tracking-wide mb-3">KATEGORI</span>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Jelajahi Berdasarkan Topik</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $categories = \App\Models\Category::all();
                $icons = [
                    'Berita' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6V7.5z"/>',
                    'Event' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>',
                    'Akademik' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>',
                    'Lomba' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0016.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.003 6.003 0 01-2.77.67h-3a6.003 6.003 0 01-2.77-.67"/>',
                ];
            @endphp

            @foreach($categories as $cat)
            <a href="{{ route('catalog', ['category' => $cat->id]) }}" class="group flex items-center gap-4 rounded-xl bg-white p-5 border border-gray-100 shadow-sm hover:shadow-md hover:border-brand-600/20 transition-all duration-300">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-brand-600/10 flex items-center justify-center text-brand-600 dark:text-brand-400 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        {!! $icons[$cat->name] ?? '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>' !!}
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white group-hover:text-brand-700 transition-colors">{{ $cat->name }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ \App\Models\Article::where('category_id', $cat->id)->where('status', 'active')->count() }} artikel</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================ --}}
{{-- CTA SECTION                                  --}}
{{-- ============================================ --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-[1440px] px-8">
        <div class="rounded-2xl bg-gradient-to-br from-[#336cbc] to-[#1e4f8f] p-10 sm:p-14 text-center relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full bg-white/5"></div>
            <div class="absolute -bottom-16 -left-16 w-40 h-40 rounded-full bg-white/5"></div>

            <div class="relative">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white mb-4">Tentang Program Studi Kami</h2>
                <p class="max-w-2xl mx-auto text-white/70 mb-8">
                    Pelajari lebih lanjut tentang visi, misi, sejarah, dan dosen pengajar Program Studi Sistem Informasi Universitas Indraprasta PGRI.
                </p>
                <a href="{{ route('about') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-8 py-3.5 text-sm font-bold text-brand-600 dark:text-brand-400 shadow-lg hover:bg-gray-50 transition-all hover:shadow-xl hover:-translate-y-0.5">
                    Kunjungi Halaman About
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

</x-layouts.app>
