<x-layouts.app :title="'#' . $tag->name . ' — SI-Pedia'" footer="min"
               :meta_description="'Artikel bertag #' . $tag->name . ' di SI-Pedia'">

<div class="bg-ink-900 py-10">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">
    <nav class="mb-3 flex items-center gap-2 text-sm text-white/50" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
      <span aria-hidden="true">›</span>
      <a href="{{ route('catalog') }}" class="hover:text-white transition">Katalog</a>
      <span aria-hidden="true">›</span>
      <span class="text-white">#{{ $tag->name }}</span>
    </nav>
    <div class="flex items-center gap-3">
      <span class="rounded-full bg-white/10 border border-white/20 px-4 py-1.5 text-2xl font-extrabold text-white">#{{ $tag->name }}</span>
    </div>
    <p class="mt-2 text-sm text-white/60">{{ $articles->total() }} artikel dengan tag ini</p>
  </div>
</div>

<main class="bg-gray-50 min-h-[60vh] py-8" id="main-content">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">

    @forelse($articles as $article)
    <article class="mb-5 flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm hover:shadow-md transition-all group">
      @if($article->image_url)
      <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
           class="h-24 w-32 flex-shrink-0 rounded-xl object-cover sm:h-28 sm:w-36" loading="lazy">
      @else
      <div class="h-24 w-32 flex-shrink-0 rounded-xl bg-gradient-to-br from-brand-50 to-brand-100 dark:from-gray-800 dark:to-gray-900 flex items-center justify-center sm:h-28 sm:w-36" aria-hidden="true">
        <svg class="h-8 w-8 text-brand-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
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
          @foreach($article->tags as $t)
          <a href="{{ route('tags.show', $t->slug) }}"
             class="rounded-full px-2 py-0.5 text-xs font-semibold transition
                    {{ $t->slug === $tag->slug ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-500 hover:bg-brand-100 hover:text-brand-700' }}">
            #{{ $t->name }}
          </a>
          @endforeach
        </div>
      </div>
    </article>

    @empty
    <div class="py-20 text-center" role="status">
      <p class="text-xl font-bold text-gray-700">Belum ada artikel dengan tag #{{ $tag->name }}</p>
      <a href="{{ route('catalog') }}" class="mt-5 inline-block rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
        Lihat Semua Artikel
      </a>
    </div>
    @endforelse

    @if($articles->hasPages())
    <div class="mt-8">{{ $articles->links() }}</div>
    @endif
  </div>
</main>
</x-layouts.app>
