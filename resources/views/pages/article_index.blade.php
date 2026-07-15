<x-layouts.admin title="Manajemen Artikel — SI-Pedia" section="articles">
<div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
    <div>
      <h1 class="page-title">Article Data</h1>
      <p class="mt-1 text-gray-700">Kelola semua artikel di sistem.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
      @if($pendingCount > 0 || $pendingDeleteCount > 0)
        <a href="{{ route('admin.articles.pending') }}"
           class="flex items-center gap-2 rounded-lg bg-yellow-400 px-4 sm:px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-yellow-500 transition">
          📋 Pending
          <span class="rounded-full bg-white px-2 py-0.5 text-xs font-black text-yellow-600">
            {{ $pendingCount + $pendingDeleteCount }}
          </span>
        </a>
      @endif
      <a href="{{ route('admin.articles.trash') }}"
         class="rounded-lg bg-gray-200 px-4 sm:px-5 py-2.5 text-sm font-bold text-gray-700 shadow hover:bg-gray-300 transition">
        🗑 Trash
      </a>
      <a href="{{ route('admin.articles.create') }}"
         class="rounded-lg bg-brand-600 px-4 sm:px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-brand-700 transition">
        + Add Article
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-xl bg-green-50 border border-green-200 px-5 py-3 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-6 flex h-14 items-center rounded-xl border border-gray-200 px-4 sm:px-5 text-gray-400 shadow-sm">
    🔍 <span class="ml-3 truncate">Search Articles by title, category, or author...</span>
  </div>

  {{-- Desktop table header (lg+ only) --}}
  <div class="mt-6 hidden lg:grid grid-cols-[60px_120px_1fr_170px_110px_170px_190px] gap-2 rounded-xl bg-tablehead px-4 py-3 text-sm font-bold text-gray-800">
    <div>No</div><div>Thumbnail</div><div>Judul Artikel</div><div>Kategori</div><div>Penulis</div><div>Tanggal / Status</div><div>Action</div>
  </div>

  <div class="mt-3 space-y-3">
    @forelse($articles as $i => $article)
    @php
      $sc = match($article->status) {
        'active'         => 'bg-status-active',
        'pending'        => 'bg-yellow-400',
        'pending_delete' => 'bg-red-500',
        default          => 'bg-gray-400',
      };
      $sl = match($article->status) {
        'active'         => 'Active',
        'pending'        => 'Pending',
        'pending_delete' => 'Req. Delete',
        default          => 'Draft',
      };
      $hasPendingEdit = isset($pendingEditArticleIds) && $article->status === 'active' && $pendingEditArticleIds->contains($article->id);
    @endphp

    {{-- ═══ Desktop row (lg+) ═══ --}}
    <div class="hidden lg:grid grid-cols-[60px_120px_1fr_170px_110px_170px_190px] items-start gap-2 rounded-2xl bg-white px-4 py-4 shadow-[0_2px_10px_rgba(0,0,0,0.06)] overflow-hidden">
      <div class="text-lg font-bold">{{ $i + 1 + ($articles->currentPage() - 1) * $articles->perPage() }}</div>
      <div>
        @if($article->image)
          <img src="{{ $article->image_url }}" class="h-[80px] w-[100px] rounded object-cover">
        @else
          <div class="h-[80px] w-[100px] rounded bg-gray-200 flex items-center justify-center text-xs text-gray-500">No Image</div>
        @endif
      </div>
      <div class="pr-4 min-w-0">
        <p class="text-sm font-bold leading-snug text-gray-900">{{ $article->title }}</p>
        @if($article->user)
          <p class="mt-1 text-xs text-gray-400">oleh {{ $article->user->name }} ({{ ucfirst($article->user->role) }})</p>
        @endif
      </div>
      <div><span class="rounded-full bg-badge-cat px-3 py-1 text-xs font-semibold text-white">{{ $article->category->name ?? 'Uncategorized' }}</span></div>
      <div class="text-sm font-bold">{{ $article->writer }}</div>
      <div>
        <p class="text-sm font-bold">{{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }}</p>
        <span class="mt-1 inline-block rounded-md {{ $sc }} px-3 py-1 text-xs font-semibold text-white">{{ $sl }}</span>
        @if($hasPendingEdit)
        <a href="{{ route('admin.articles.revisions', $article) }}" class="mt-1 block w-fit rounded-md bg-blue-100 px-3 py-1 text-[11px] font-bold text-blue-700 hover:bg-blue-200">✏️ Ada Revisi</a>
        @endif
      </div>

      {{-- Action grid: posisi TETAP 2x2 apapun kombinasi tombolnya —
           top-left=Edit/Acc/Batal, top-right=Takedown/Tolak,
           bottom-left=Preview (selalu), bottom-right=Hapus (selalu). --}}
      <div class="grid grid-cols-2 gap-1.5">
        {{-- top-left --}}
        @if($article->status === 'pending')
          <form action="{{ route('admin.articles.approve', $article) }}" method="POST">
            @csrf @method('PATCH')
            <button class="flex h-8 w-full items-center justify-center rounded-md bg-green-500 text-[11px] font-bold text-white hover:bg-green-600">✅ Acc</button>
          </form>
        @elseif($article->status === 'pending_delete')
          <form action="{{ route('admin.articles.rejectDelete', $article) }}" method="POST">
            @csrf @method('PATCH')
            <button class="flex h-8 w-full items-center justify-center rounded-md bg-gray-200 text-[11px] font-bold text-gray-700 hover:bg-gray-300">↩ Batal</button>
          </form>
        @elseif($article->user_id === auth()->id())
          <a href="{{ route('admin.articles.edit', $article) }}"
             class="flex h-8 w-full items-center justify-center rounded-md bg-edit text-xs font-bold text-black">Edit</a>
        @else
          <div></div>
        @endif

        {{-- top-right --}}
        @if($article->status === 'pending')
          <form action="{{ route('admin.articles.reject', $article) }}" method="POST">
            @csrf @method('PATCH')
            <button class="flex h-8 w-full items-center justify-center rounded-md bg-orange-400 text-[11px] font-bold text-white hover:bg-orange-500">❌ Tolak</button>
          </form>
        @elseif($article->status === 'active')
          <a href="{{ route('admin.articles.takedownForm', $article) }}"
             class="flex h-8 w-full items-center justify-center rounded-md bg-purple-500 text-[11px] font-bold text-white hover:bg-purple-600">⬇ Takedown</a>
        @else
          <div></div>
        @endif

        {{-- bottom-left: Preview, selalu --}}
        <a href="{{ route('admin.articles.preview', $article) }}" target="_blank"
           class="flex h-8 w-full items-center justify-center rounded-md bg-blue-500 text-[11px] font-bold text-white hover:bg-blue-600">👁 Preview</a>

        {{-- bottom-right: Hapus, selalu --}}
        @if($article->status === 'pending_delete')
          <form action="{{ route('admin.articles.approveDelete', $article) }}" method="POST"
                onsubmit="return confirm('Pindahkan artikel ini ke Trash?')">
            @csrf @method('DELETE')
            <button class="flex h-8 w-full items-center justify-center rounded-md bg-red-500 text-[11px] font-bold text-white hover:bg-red-600">🗑 Hapus</button>
          </form>
        @else
          <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                onsubmit="return confirm('Pindahkan artikel ini ke Trash? Bisa dipulihkan nanti.')">
            @csrf @method('DELETE')
            <button class="flex h-8 w-full items-center justify-center rounded-md bg-danger text-xs font-bold text-white">Hapus</button>
          </form>
        @endif
      </div>
    </div>

    {{-- ═══ Mobile card (below lg) ═══ --}}
    <div class="lg:hidden rounded-2xl bg-white p-4 shadow-[0_2px_10px_rgba(0,0,0,0.06)]">
      <div class="flex gap-3">
        @if($article->image)
          <img src="{{ $article->image_url }}" class="h-16 w-16 flex-shrink-0 rounded object-cover">
        @else
          <div class="h-16 w-16 flex-shrink-0 rounded bg-gray-200 flex items-center justify-center text-[10px] text-gray-500">No Img</div>
        @endif
        <div class="min-w-0 flex-1">
          <p class="text-sm font-bold leading-snug text-gray-900 break-words">{{ $article->title }}</p>
          @if($article->user)
            <p class="mt-0.5 text-xs text-gray-400">oleh {{ $article->user->name }} ({{ ucfirst($article->user->role) }})</p>
          @endif
          <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
            <span class="rounded-full bg-badge-cat px-2.5 py-0.5 text-[10px] font-semibold text-white">{{ $article->category->name ?? 'Uncategorized' }}</span>
            <span class="rounded-md {{ $sc }} px-2.5 py-0.5 text-[10px] font-semibold text-white">{{ $sl }}</span>
            @if($hasPendingEdit)
            <a href="{{ route('admin.articles.revisions', $article) }}" class="rounded-md bg-blue-100 px-2.5 py-0.5 text-[10px] font-bold text-blue-700">✏️ Ada Revisi</a>
            @endif
          </div>
          <p class="mt-1 text-[11px] text-gray-400">{{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }} · {{ $article->writer }}</p>
        </div>
      </div>

      <div class="mt-3 grid grid-cols-2 gap-1.5 border-t border-gray-100 pt-3">
        {{-- top-left --}}
        @if($article->status === 'pending')
          <form action="{{ route('admin.articles.approve', $article) }}" method="POST">
            @csrf @method('PATCH')
            <button class="flex h-9 w-full items-center justify-center rounded-md bg-green-500 text-xs font-bold text-white hover:bg-green-600">✅ Acc</button>
          </form>
        @elseif($article->status === 'pending_delete')
          <form action="{{ route('admin.articles.rejectDelete', $article) }}" method="POST">
            @csrf @method('PATCH')
            <button class="flex h-9 w-full items-center justify-center rounded-md bg-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-300">↩ Batal</button>
          </form>
        @elseif($article->user_id === auth()->id())
          <a href="{{ route('admin.articles.edit', $article) }}"
             class="flex h-9 w-full items-center justify-center rounded-md bg-edit text-xs font-bold text-black">Edit</a>
        @else
          <div></div>
        @endif

        {{-- top-right --}}
        @if($article->status === 'pending')
          <form action="{{ route('admin.articles.reject', $article) }}" method="POST">
            @csrf @method('PATCH')
            <button class="flex h-9 w-full items-center justify-center rounded-md bg-orange-400 text-xs font-bold text-white hover:bg-orange-500">❌ Tolak</button>
          </form>
        @elseif($article->status === 'active')
          <a href="{{ route('admin.articles.takedownForm', $article) }}"
             class="flex h-9 w-full items-center justify-center rounded-md bg-purple-500 text-xs font-bold text-white hover:bg-purple-600">⬇ Takedown</a>
        @else
          <div></div>
        @endif

        {{-- bottom-left: Preview, selalu --}}
        <a href="{{ route('admin.articles.preview', $article) }}" target="_blank"
           class="flex h-9 w-full items-center justify-center rounded-md bg-blue-500 text-xs font-bold text-white hover:bg-blue-600">👁 Preview</a>

        {{-- bottom-right: Hapus, selalu --}}
        @if($article->status === 'pending_delete')
          <form action="{{ route('admin.articles.approveDelete', $article) }}" method="POST"
                onsubmit="return confirm('Pindahkan artikel ini ke Trash?')">
            @csrf @method('DELETE')
            <button class="flex h-9 w-full items-center justify-center rounded-md bg-red-500 text-xs font-bold text-white hover:bg-red-600">🗑 Hapus</button>
          </form>
        @else
          <form action="{{ route('admin.articles.destroy', $article) }}" method="POST"
                onsubmit="return confirm('Pindahkan artikel ini ke Trash? Bisa dipulihkan nanti.')">
            @csrf @method('DELETE')
            <button class="flex h-9 w-full items-center justify-center rounded-md bg-danger text-xs font-bold text-white">Hapus</button>
          </form>
        @endif
      </div>
    </div>
    @empty
    <div class="p-8 text-center text-gray-500 bg-white rounded-2xl shadow-[0_2px_10px_rgba(0,0,0,0.06)]">
      Tidak ada artikel.
    </div>
    @endforelse
  </div>

  <div class="mt-6">{{ $articles->links() }}</div>
</div>
</x-layouts.admin>
