<x-layouts.admin title="Riwayat Revisi — SI-Pedia" section="articles">

<div class="mb-5 flex items-center gap-3">
  <a href="{{ route('admin.articles.edit', $article) }}" class="rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50 transition" aria-label="Kembali">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
  </a>
  <div>
    <h2 class="text-xl font-extrabold text-gray-900">Riwayat Revisi</h2>
    <p class="text-sm text-gray-500 truncate max-w-[400px]">{{ $article->title }}</p>
  </div>
</div>

<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
  @forelse($revisions as $i => $rev)
  <div class="flex items-start gap-4 border-b border-gray-100 p-5 last:border-0 hover:bg-gray-50 transition-colors">
    <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-brand-600/10 text-xs font-bold text-brand-700">
      {{ $revisions->count() - $i }}
    </div>
    <div class="flex-1 min-w-0">
      <div class="flex flex-wrap items-center gap-3 mb-1">
        <span class="text-sm font-bold text-gray-900">{{ $rev->title }}</span>
        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold
          {{ $rev->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' :
             ($rev->status === 'draft' ? 'bg-gray-100 text-gray-600' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300') }}">
          {{ ucfirst($rev->status) }}
        </span>
        @if($i === 0)
        <span class="rounded-full bg-brand-600 px-2 py-0.5 text-[10px] font-bold text-white">Terbaru</span>
        @endif
      </div>
      <div class="flex flex-wrap items-center gap-3 text-xs text-gray-400">
        <span>{{ $rev->user->name ?? 'System' }}</span>
        <span>·</span>
        <time datetime="{{ $rev->created_at->toISOString() }}">{{ $rev->created_at->translatedFormat('j M Y, H:i') }}</time>
        @if($rev->revision_note)
        <span>· <em>{{ $rev->revision_note }}</em></span>
        @endif
      </div>
    </div>
    <div class="text-xs text-gray-400 text-right flex-shrink-0">
      {{ number_format(str_word_count(strip_tags($rev->content))) }} kata
    </div>
  </div>
  @empty
  <div class="py-12 text-center text-sm text-gray-400">Belum ada riwayat revisi.</div>
  @endforelse
</div>

</x-layouts.admin>
