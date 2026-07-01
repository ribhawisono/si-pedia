<x-layouts.app title="Bookmark Saya — SI-Pedia" footer="min">

<div class="bg-ink-900 py-10">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-extrabold text-white">🔖 Bookmark Saya</h1>
    <p class="mt-1 text-sm text-white/60">Artikel yang kamu simpan untuk dibaca nanti.</p>
  </div>
</div>

<main class="bg-gray-50 min-h-[60vh] py-8" id="main-content">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">

    @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm font-semibold text-green-700" role="alert">
      ✅ {{ session('success') }}
    </div>
    @endif

    @forelse($bookmarks as $article)
    <article class="mb-5 flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm hover:shadow-md transition-all group">
      @if($article->image_url)
      <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
           class="h-24 w-32 flex-shrink-0 rounded-xl object-cover sm:h-28 sm:w-36" loading="lazy">
      @else
      <div class="h-24 w-32 flex-shrink-0 rounded-xl bg-gradient-to-br from-brand-50 to-brand-100 flex items-center justify-center sm:h-28 sm:w-36" aria-hidden="true">
        <svg class="h-8 w-8 text-brand-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5"/>
        </svg>
      </div>
      @endif
      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-2">
          <span class="rounded-md bg-brand-600/10 px-2.5 py-0.5 text-xs font-semibold text-brand-700">{{ $article->category->name ?? 'Umum' }}</span>
          <time class="text-xs text-gray-400" datetime="{{ $article->created_at->toISOString() }}">{{ $article->created_at->translatedFormat('j M Y') }}</time>
          <span class="text-xs text-gray-400">{{ $article->reading_time }} mnt</span>
        </div>
        <h2 class="font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-brand-700 transition-colors">
          <a href="{{ route('articles.show', $article->slug) }}">{{ $article->title }}</a>
        </h2>
        <p class="mt-1.5 text-sm text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($article->content), 120) }}</p>
        <div class="mt-2 flex flex-wrap gap-2">
          @foreach($article->tags as $tag)
          <a href="{{ route('tags.show', $tag->slug) }}" class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 hover:bg-brand-100 hover:text-brand-700 transition">
            #{{ $tag->name }}
          </a>
          @endforeach
        </div>
      </div>
      <div class="flex-shrink-0 flex items-start gap-2">
        <a href="{{ route('articles.show', $article->slug) }}"
           class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-semibold text-white hover:bg-brand-700 transition">
          Baca →
        </a>
        <form action="{{ route('bookmarks.toggle', $article) }}" method="POST">
          @csrf
          <button type="submit" aria-label="Hapus bookmark: {{ $article->title }}"
                  class="rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition">
            🗑
          </button>
        </form>
      </div>
    </article>

    @empty
    <div class="flex flex-col items-center justify-center py-24 text-center" role="status">
      <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100" aria-hidden="true">
        <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
        </svg>
      </div>
      <p class="text-xl font-bold text-gray-700">Belum ada artikel tersimpan</p>
      <p class="mt-2 text-sm text-gray-400">Klik tombol 🔖 Simpan saat membaca artikel untuk menyimpannya di sini.</p>
      <a href="{{ route('catalog') }}" class="mt-5 rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
        Jelajahi Artikel
      </a>
    </div>
    @endforelse

    @if($bookmarks->hasPages())
    <div class="mt-8">{{ $bookmarks->links() }}</div>
    @endif
  </div>
</main>
</x-layouts.app>
