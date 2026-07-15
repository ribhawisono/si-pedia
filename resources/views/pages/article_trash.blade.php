<x-layouts.admin title="Trash Artikel — SI-Pedia" section="articles">
<main class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
    <div>
      <h1 class="page-title">Trash Artikel</h1>
      <p class="mt-1 text-gray-700">Artikel yang dihapus (dari admin maupun user) atau ditakedown. Bisa dipulihkan atau dihapus permanen.</p>
    </div>
    <a href="{{ route('admin.articles.index') }}"
       class="self-start rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-bold text-gray-700 shadow hover:bg-gray-200 transition">
      ← Kembali
    </a>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-xl bg-green-50 border border-green-200 px-5 py-3 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif

  {{-- Desktop table header (lg+ only) --}}
  <div class="mt-6 hidden lg:grid grid-cols-[60px_1fr_130px_110px_100px_150px_200px] gap-2 rounded-xl bg-tablehead px-4 py-3 text-sm font-bold text-gray-800">
    <div>No</div><div>Judul Artikel</div><div>Kategori</div><div>Penulis</div><div>Alasan</div><div>Dihapus</div><div>Action</div>
  </div>

  <div class="mt-3 space-y-3">
    @forelse($articles as $i => $article)
    {{-- ═══ Desktop row (lg+) ═══ --}}
    <div class="hidden lg:grid grid-cols-[60px_1fr_130px_110px_100px_150px_200px] items-start gap-2 rounded-2xl bg-white px-4 py-4 shadow-[0_2px_10px_rgba(0,0,0,0.06)] overflow-hidden">
      <div class="text-lg font-bold">{{ $i + 1 + ($articles->currentPage() - 1) * $articles->perPage() }}</div>
      <div class="pr-4 min-w-0">
        <p class="text-sm font-bold leading-snug text-gray-900">{{ $article->title }}</p>
        @if($article->user)
          <p class="mt-1 text-xs text-gray-400">oleh {{ $article->user->name }}</p>
        @endif
        @if($article->trashed_reason === 'takedown' && $article->rejection_note)
        <p class="mt-1 text-xs text-purple-600">📝 {{ Str::limit($article->rejection_note, 80) }}</p>
        @endif
      </div>
      <div><span class="rounded-full bg-badge-cat px-3 py-1 text-xs font-semibold text-white">{{ $article->category->name ?? 'Uncategorized' }}</span></div>
      <div class="text-sm font-bold">{{ $article->writer }}</div>
      <div>
        @if($article->trashed_reason === 'takedown')
          <span class="rounded-md bg-purple-100 px-2.5 py-1 text-[11px] font-bold text-purple-700">Takedown</span>
          <p class="mt-1 text-[10px] text-purple-500">Bisa diedit oleh {{ $article->user->name ?? $article->writer }}</p>
        @else
          <span class="rounded-md bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-700">Hapus</span>
        @endif
      </div>
      <div class="text-sm font-bold">{{ $article->deleted_at->translatedFormat('j F Y, H:i') }}</div>
      <div class="flex flex-wrap gap-1.5 min-w-0">
        <form action="{{ route('admin.articles.restore', $article->id) }}" method="POST" class="inline"
              onsubmit="return confirm('Pulihkan artikel ini sebagai Draft?')">
          @csrf @method('PATCH')
          <button class="rounded-md bg-green-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-green-600">↩ Pulihkan</button>
        </form>
        <form action="{{ route('admin.articles.forceDelete', $article->id) }}" method="POST" class="inline"
              onsubmit="return confirm('Hapus PERMANEN artikel ini? Tindakan ini tidak bisa dibatalkan.')">
          @csrf @method('DELETE')
          <button class="rounded-md bg-danger px-3 py-1.5 text-xs font-bold text-white">🗑 Hapus Permanen</button>
        </form>
      </div>
    </div>

    {{-- ═══ Mobile card (below lg) ═══ --}}
    <div class="lg:hidden rounded-2xl bg-white p-4 shadow-[0_2px_10px_rgba(0,0,0,0.06)]">
      <p class="text-sm font-bold leading-snug text-gray-900 break-words">{{ $article->title }}</p>
      @if($article->user)
        <p class="mt-0.5 text-xs text-gray-400">oleh {{ $article->user->name }}</p>
      @endif
      @if($article->trashed_reason === 'takedown' && $article->rejection_note)
      <p class="mt-1 text-xs text-purple-600">📝 {{ Str::limit($article->rejection_note, 100) }}</p>
      @endif

      <div class="mt-2 flex flex-wrap items-center gap-1.5">
        <span class="rounded-full bg-badge-cat px-2.5 py-0.5 text-[10px] font-semibold text-white">{{ $article->category->name ?? 'Uncategorized' }}</span>
        @if($article->trashed_reason === 'takedown')
          <span class="rounded-md bg-purple-100 px-2.5 py-0.5 text-[10px] font-bold text-purple-700">Takedown — bisa diedit oleh {{ $article->user->name ?? $article->writer }}</span>
        @else
          <span class="rounded-md bg-red-100 px-2.5 py-0.5 text-[10px] font-bold text-red-700">Hapus</span>
        @endif
      </div>
      <p class="mt-1.5 text-[11px] text-gray-400">{{ $article->writer }} · dihapus {{ $article->deleted_at->translatedFormat('j F Y, H:i') }}</p>

      <div class="mt-3 flex gap-1.5 border-t border-gray-100 pt-3">
        <form action="{{ route('admin.articles.restore', $article->id) }}" method="POST" class="flex-1"
              onsubmit="return confirm('Pulihkan artikel ini sebagai Draft?')">
          @csrf @method('PATCH')
          <button class="flex h-9 w-full items-center justify-center rounded-md bg-green-500 text-xs font-bold text-white hover:bg-green-600">↩ Pulihkan</button>
        </form>
        <form action="{{ route('admin.articles.forceDelete', $article->id) }}" method="POST" class="flex-1"
              onsubmit="return confirm('Hapus PERMANEN artikel ini? Tindakan ini tidak bisa dibatalkan.')">
          @csrf @method('DELETE')
          <button class="flex h-9 w-full items-center justify-center rounded-md bg-danger text-xs font-bold text-white">🗑 Hapus Permanen</button>
        </form>
      </div>
    </div>
    @empty
    <div class="p-8 text-center text-gray-500 bg-white rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.06)]">
      Trash kosong.
    </div>
    @endforelse
  </div>

  <div class="mt-6">{{ $articles->links() }}</div>
</main>
</x-layouts.admin>
