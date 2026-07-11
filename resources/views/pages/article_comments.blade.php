<x-layouts.app :title="'Komentar: ' . $article->title . ' — SI-Pedia'" footer="min">

<div class="bg-ink-900 py-8">
  <div class="mx-auto max-w-[800px] px-4 sm:px-6">
    <nav class="mb-3 flex items-center gap-2 text-xs text-white/50" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
      <span aria-hidden="true">›</span>
      <a href="{{ route('articles.show', $article->slug) }}" class="hover:text-white transition truncate max-w-[200px]">{{ $article->title }}</a>
      <span aria-hidden="true">›</span>
      <span class="text-white">Komentar</span>
    </nav>
    <h1 class="text-xl font-extrabold text-white">Komentar untuk “{{ $article->title }}”</h1>
    <p class="mt-1 text-sm text-white/60">{{ $comments->total() }} komentar</p>
  </div>
</div>

<main class="bg-gray-50 min-h-[50vh] py-8" id="main-content">
  <div class="mx-auto max-w-[800px] px-4 sm:px-6">

    @forelse($comments as $comment)
    <div class="mb-4 flex gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
      <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name ?? 'A') . '&background=336cbc&color=fff&size=80' }}"
           alt="Foto {{ $comment->user->name ?? 'Anonim' }}"
           class="h-9 w-9 flex-shrink-0 rounded-full object-cover" loading="lazy">
      <div class="min-w-0 flex-1">
        <div class="mb-1 flex flex-wrap items-center gap-2">
          <span class="text-sm font-bold text-gray-900">{{ $comment->user->name ?? 'Anonim' }}</span>
          <time datetime="{{ $comment->created_at->toISOString() }}" class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</time>
        </div>
        <p class="text-sm text-gray-700 text-justify break-words">{{ $comment->content }}</p>
      </div>
    </div>
    @empty
    <div class="py-16 text-center" role="status">
      <p class="text-gray-400">Belum ada komentar untuk artikel ini.</p>
    </div>
    @endforelse

    @if($comments->hasPages())
    <div class="mt-6">{{ $comments->links() }}</div>
    @endif

    <a href="{{ route('articles.show', $article->slug) }}"
       class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brand-600 hover:text-brand-700">
      ← Kembali ke artikel
    </a>
  </div>
</main>
</x-layouts.app>
