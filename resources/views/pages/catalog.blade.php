<x-layouts.app title="Katalog Artikel — SI-Pedia" active="Catalog" footer="full"
               meta_description="Jelajahi semua artikel Program Studi Sistem Informasi Universitas Indraprasta PGRI.">

{{-- ─── Filter & Sort Header ──────────────────────────────────────────────── --}}
<div class="bg-ink-900 py-10">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-extrabold text-white mb-1">Katalog Artikel</h1>
    <p class="text-white/60 text-sm mb-6">Eksplorasi artikel akademik dan informasi Program Studi SI.</p>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('catalog') }}" role="search" id="catalog-form">
      <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4" aria-hidden="true">
            <svg class="h-4 w-4 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
            </svg>
          </div>
          <label for="catalog-search" class="sr-only">Cari artikel</label>
          <input id="catalog-search" type="search" name="q" value="{{ $q ?? '' }}"
                 placeholder="Cari artikel, penulis..."
                 class="w-full rounded-xl bg-white/10 border border-white/20 py-3 pl-11 pr-4 text-sm text-white placeholder:text-white/40 focus:bg-white/15 focus:border-white/40 focus:outline-none transition-all">
        </div>
        {{-- Sort --}}
        <label for="sort-select" class="sr-only">Urutkan artikel</label>
        <select id="sort-select" name="sort" onchange="document.getElementById('catalog-form').submit()"
                class="rounded-xl bg-white/10 border border-white/20 py-3 px-4 text-sm text-white focus:outline-none focus:border-white/40 cursor-pointer min-w-[160px]">
          <option value="newest"      @selected(($sort??'newest')==='newest')>🕐 Terbaru</option>
          <option value="oldest"      @selected(($sort??'')==='oldest')>📅 Terlama</option>
          <option value="most_viewed" @selected(($sort??'')==='most_viewed')>👁 Terpopuler</option>
          <option value="alpha"       @selected(($sort??'')==='alpha')>🔤 A–Z</option>
          <option value="trending"    @selected(($sort??'')==='trending')>🔥 Trending</option>
        </select>
        <input type="hidden" name="category" value="{{ request('category') }}">
        <input type="hidden" name="tag" value="{{ request('tag') }}">
        <button type="submit" class="rounded-xl bg-brand-600 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">
          Cari
        </button>
      </div>
    </form>
  </div>
</div>

<main class="bg-gray-50 min-h-[60vh]" id="main-content">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

      {{-- ─── Sidebar Filters ──────────────────────────────────────────── --}}
      <aside class="lg:w-64 flex-shrink-0" aria-label="Filter artikel">
        {{-- Category filter --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm mb-4">
          <h2 class="mb-3 text-sm font-extrabold uppercase tracking-wide text-gray-500">Kategori</h2>
          <div class="space-y-1">
            <a href="{{ route('catalog', array_merge(request()->except('category', 'page'), ['sort' => $sort])) }}"
               class="flex items-center justify-between rounded-lg px-3 py-2 text-sm transition
                      {{ !request('category') ? 'bg-brand-600/10 font-semibold text-brand-700' : 'text-gray-600 hover:bg-gray-50' }}">
              <span>Semua</span>
              <span class="rounded-full bg-gray-100 px-2 text-xs text-gray-500">{{ $articles->total() }}</span>
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('catalog', array_merge(request()->except('category','page'), ['category' => $cat->id, 'sort' => $sort])) }}"
               class="flex items-center justify-between rounded-lg px-3 py-2 text-sm transition
                      {{ request('category') == $cat->id ? 'bg-brand-600/10 font-semibold text-brand-700' : 'text-gray-600 hover:bg-gray-50' }}">
              <span>{{ $cat->name }}</span>
              <span class="rounded-full bg-gray-100 px-2 text-xs text-gray-500">{{ $cat->articles_count }}</span>
            </a>
            @endforeach
          </div>
        </div>

        {{-- Tags filter --}}
        @if($tags->isNotEmpty())
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
          <h2 class="mb-3 text-sm font-extrabold uppercase tracking-wide text-gray-500">Tag Populer</h2>
          <div class="flex flex-wrap gap-2">
            @foreach($tags as $tag)
            <a href="{{ route('catalog', array_merge(request()->except('tag','page'), ['tag' => $tag->slug, 'sort' => $sort])) }}"
               class="rounded-full border px-3 py-1 text-xs font-semibold transition
                      {{ request('tag') === $tag->slug ? 'border-brand-600 bg-brand-600/10 text-brand-700' : 'border-gray-200 text-gray-600 hover:border-brand-300 hover:text-brand-700' }}">
              #{{ $tag->name }}
              <span class="ml-1 text-gray-400">{{ $tag->articles_count }}</span>
            </a>
            @endforeach
          </div>
        </div>
        @endif
      </aside>

      {{-- ─── Articles Grid ─────────────────────────────────────────────── --}}
      <div class="flex-1 min-w-0">
        {{-- Active filters & results info --}}
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
          <p class="text-sm text-gray-500" role="status" aria-live="polite">
            @if($articles->total())
              Menampilkan <strong class="text-gray-900">{{ $articles->firstItem() }}–{{ $articles->lastItem() }}</strong>
              dari <strong class="text-gray-900">{{ $articles->total() }}</strong> artikel
              @if($q ?? false) untuk "<strong class="text-brand-700">{{ $q }}</strong>" @endif
            @else
              Tidak ada artikel yang ditemukan.
            @endif
          </p>
          {{-- Clear filters --}}
          @if(request()->hasAny(['q','category','tag']))
          <a href="{{ route('catalog') }}" class="text-sm font-semibold text-red-500 hover:text-red-700 transition">
            ✕ Hapus filter
          </a>
          @endif
        </div>

        @forelse($articles as $article)
        <article class="mb-5 flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm hover:shadow-md transition-all group focus-within:ring-2 focus-within:ring-brand-600">
          {{-- Thumbnail --}}
          <div class="flex-shrink-0">
            @if($article->image_url)
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                 class="h-24 w-32 rounded-xl object-cover sm:h-28 sm:w-36"
                 loading="lazy">
            @else
            <div class="flex h-24 w-32 items-center justify-center rounded-xl bg-gradient-to-br from-brand-50 to-brand-100 sm:h-28 sm:w-36" aria-hidden="true">
              <svg class="h-10 w-10 text-brand-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9.75m3 0l-3-3m3 3l-3 3"/>
              </svg>
            </div>
            @endif
          </div>

          {{-- Content --}}
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2">
              <span class="rounded-md bg-brand-600/10 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
                {{ $article->category->name ?? 'Umum' }}
              </span>
              <span class="text-xs text-gray-400">{{ $article->created_at->translatedFormat('j M Y') }}</span>
              <span class="text-xs text-gray-400 flex items-center gap-1">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $article->reading_time }} mnt
              </span>
            </div>
            <h2 class="font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-brand-700 transition-colors">
              <a href="{{ route('articles.show', $article->slug) }}" class="focus:outline-none focus:underline">
                {{ $article->title }}
              </a>
            </h2>
            <p class="mt-1.5 text-sm text-gray-500 line-clamp-2">
              {{ Str::limit(strip_tags($article->content), 130) }}
            </p>
            <div class="mt-2 flex flex-wrap items-center gap-3">
              <span class="text-xs text-gray-400">{{ $article->writer }}</span>
              <span class="text-xs text-gray-300">·</span>
              <span class="flex items-center gap-1 text-xs text-gray-400">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ number_format($article->views) }}
              </span>
              @foreach($article->tags->take(3) as $tag)
              <a href="{{ route('tags.show', $tag->slug) }}"
                 class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 hover:bg-brand-100 hover:text-brand-700 transition">
                #{{ $tag->name }}
              </a>
              @endforeach
            </div>
          </div>

          {{-- Read button --}}
          <div class="hidden flex-shrink-0 items-center sm:flex">
            <a href="{{ route('articles.show', $article->slug) }}"
               class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400"
               aria-label="Baca artikel: {{ $article->title }}">
              Baca →
            </a>
          </div>
        </article>

        @empty
        {{-- Empty state --}}
        <div class="flex flex-col items-center justify-center py-24 text-center" role="status">
          <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100" aria-hidden="true">
            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
          </div>
          <p class="text-xl font-bold text-gray-700">Tidak ada artikel ditemukan</p>
          <p class="mt-2 text-sm text-gray-400">Coba kata kunci atau filter yang berbeda</p>
          <a href="{{ route('catalog') }}" class="mt-5 rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
            Lihat Semua Artikel
          </a>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($articles->hasPages())
        <div class="mt-8" role="navigation" aria-label="Navigasi halaman">
          {{ $articles->links() }}
        </div>
        @endif
      </div>
    </div>
  </div>
</main>
</x-layouts.app>
