<x-layouts.app :title="'Pencarian: ' . ($q ?: 'SI-Pedia') . ' — SI-Pedia'" footer="min"
               :meta_description="'Hasil pencarian untuk: ' . $q">

<main class="min-h-[60vh] bg-gray-50" id="main-content">

  {{-- Search Header --}}
  <div class="bg-ink-900 py-10">
    <div class="mx-auto max-w-[860px] px-6">
      <h1 class="mb-4 text-2xl font-extrabold text-white">
        @if($q) Hasil pencarian untuk "<span class="text-brand-300">{{ $q }}</span>"
        @else Cari Konten SI-Pedia
        @endif
      </h1>
      <form action="{{ route('search') }}" method="GET" role="search">
        <label for="search-page-input" class="sr-only">Kata kunci pencarian</label>
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4" aria-hidden="true">
            <svg class="h-5 w-5 text-white/40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
            </svg>
          </div>
          <input id="search-page-input" type="search" name="q" value="{{ $q }}" autofocus
                 placeholder="Cari artikel, dosen, kategori..."
                 class="w-full rounded-xl bg-white/10 border border-white/20 py-3 pl-11 pr-4 text-white placeholder:text-white/40 focus:bg-white/15 focus:border-white/40 focus:outline-none text-sm transition-all">
          <button type="submit"
                  class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-brand-600 px-4 py-1.5 text-sm font-semibold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">
            Cari
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="mx-auto max-w-[860px] px-6 py-10">

    @if(!$q)
    {{-- No query state --}}
    <div class="py-16 text-center" role="status">
      <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100">
        <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
        </svg>
      </div>
      <p class="text-xl font-bold text-gray-700">Masukkan kata kunci untuk mulai mencari</p>
      <p class="mt-2 text-sm text-gray-400">Cari artikel, dosen, atau kategori di SI-Pedia</p>
    </div>

    @elseif($totalResults === 0)
    {{-- No results --}}
    <div class="py-16 text-center" role="status" aria-live="polite">
      <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100">
        <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/>
        </svg>
      </div>
      <p class="text-xl font-bold text-gray-700">Tidak ditemukan hasil untuk "{{ $q }}"</p>
      <p class="mt-2 text-sm text-gray-400">Coba kata kunci yang berbeda atau lebih umum</p>
      <div class="mt-6 flex flex-wrap justify-center gap-3 text-sm">
        <a href="{{ route('catalog') }}" class="rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50 transition">Lihat Semua Artikel</a>
        <a href="{{ route('about') }}" class="rounded-lg border border-gray-300 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-50 transition">Tentang Prodi</a>
      </div>
    </div>

    @else
    {{-- Results summary --}}
    <div class="mb-6 flex items-center justify-between" role="status" aria-live="polite">
      <p class="text-sm text-gray-500">
        Ditemukan <strong class="text-gray-900">{{ $totalResults }}</strong> hasil
        @if($q) untuk "<strong class="text-brand-700">{{ $q }}</strong>" @endif
      </p>
    </div>

    {{-- Articles --}}
    @if($articles->isNotEmpty())
    <section class="mb-10" aria-labelledby="articles-heading">
      <h2 id="articles-heading" class="mb-4 flex items-center gap-2 text-lg font-extrabold text-gray-900">
        <span class="rounded-lg bg-brand-600/10 p-1.5 text-brand-600" aria-hidden="true">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </span>
        Artikel
        <span class="ml-auto rounded-full bg-gray-100 px-2.5 py-0.5 text-sm font-semibold text-gray-600">{{ $articles->count() }}</span>
      </h2>
      <div class="space-y-3">
        @foreach($articles as $article)
        <a href="{{ route('articles.show', $article->slug) }}"
           class="flex gap-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm hover:shadow-md hover:border-brand-100 transition-all group focus:outline-none focus:ring-2 focus:ring-brand-600"
           aria-label="{{ $article->title }}">
          @if($article->image)
          <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
               class="h-20 w-28 flex-shrink-0 rounded-lg object-cover" loading="lazy">
          @else
          <div class="h-20 w-28 flex-shrink-0 rounded-lg bg-gray-100 flex items-center justify-center" aria-hidden="true">
            <svg class="h-8 w-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
          </div>
          @endif
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <span class="rounded-md bg-brand-600/10 px-2 py-0.5 text-xs font-semibold text-brand-700">
                {{ $article->category->name ?? 'Umum' }}
              </span>
              <span class="text-xs text-gray-400">{{ $article->created_at->translatedFormat('j M Y') }}</span>
            </div>
            <h3 class="font-bold text-gray-900 group-hover:text-brand-700 transition-colors line-clamp-2 leading-snug">
              {{ $article->title }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 line-clamp-2">
              {{ Str::limit(strip_tags($article->content), 120) }}
            </p>
          </div>
        </a>
        @endforeach
      </div>
    </section>
    @endif

    {{-- Lecturers --}}
    @if($lecturers->isNotEmpty())
    <section class="mb-10" aria-labelledby="lecturers-heading">
      <h2 id="lecturers-heading" class="mb-4 flex items-center gap-2 text-lg font-extrabold text-gray-900">
        <span class="rounded-lg bg-green-600/10 p-1.5 text-green-600" aria-hidden="true">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
        </span>
        Dosen
        <span class="ml-auto rounded-full bg-gray-100 px-2.5 py-0.5 text-sm font-semibold text-gray-600">{{ $lecturers->count() }}</span>
      </h2>
      <div class="grid gap-3 sm:grid-cols-2">
        @foreach($lecturers as $lecturer)
        <div class="flex items-center gap-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
          <img src="{{ $lecturer->photo ? (str_starts_with($lecturer->photo,'http') ? $lecturer->photo : \Illuminate\Support\Facades\Storage::url($lecturer->photo)) : 'https://ui-avatars.com/api/?name='.urlencode($lecturer->user->name??'Dosen').'&background=336cbc&color=fff&size=80' }}"
               alt="Foto {{ $lecturer->user->name ?? 'Dosen' }}"
               class="h-12 w-12 flex-shrink-0 rounded-full object-cover" loading="lazy">
          <div class="min-w-0">
            <p class="font-bold text-gray-900 truncate">{{ $lecturer->user->name ?? '—' }}</p>
            <p class="text-xs text-gray-500">NIDN: {{ $lecturer->nidn ?? '—' }}</p>
            <p class="text-xs text-gray-400 truncate">{{ $lecturer->address ?? '—' }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </section>
    @endif

    {{-- Categories --}}
    @if($categories->isNotEmpty())
    <section aria-labelledby="categories-heading">
      <h2 id="categories-heading" class="mb-4 flex items-center gap-2 text-lg font-extrabold text-gray-900">
        <span class="rounded-lg bg-purple-600/10 p-1.5 text-purple-600" aria-hidden="true">
          <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z"/>
          </svg>
        </span>
        Kategori
        <span class="ml-auto rounded-full bg-gray-100 px-2.5 py-0.5 text-sm font-semibold text-gray-600">{{ $categories->count() }}</span>
      </h2>
      <div class="flex flex-wrap gap-3">
        @foreach($categories as $cat)
        <a href="{{ route('catalog', ['category' => $cat->id]) }}"
           class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:border-brand-300 hover:text-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-600">
          {{ $cat->name }}
          <span class="rounded-full bg-gray-100 px-1.5 py-0.5 text-xs text-gray-500">{{ $cat->articles_count }}</span>
        </a>
        @endforeach
      </div>
    </section>
    @endif

    @endif {{-- end if results --}}
  </div>
</main>
</x-layouts.app>
