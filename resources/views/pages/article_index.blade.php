<x-layouts.app title="Article Data — SI-Pedia">
<main class="mx-auto max-w-[1440px] px-8 py-8">
  <div class="flex items-start justify-between">
    <div>
      <h1 class="text-5xl font-black tracking-tight">Article Data</h1>
      <p class="mt-1 text-gray-700">Kelola semua artikel di sistem.</p>
    </div>
    <div class="flex items-center gap-3">
      @if($pendingCount > 0 || $pendingDeleteCount > 0)
        <a href="{{ route('admin.articles.pending') }}"
           class="flex items-center gap-2 rounded-lg bg-yellow-400 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-yellow-500 transition">
          📋 Pending
          <span class="rounded-full bg-white px-2 py-0.5 text-xs font-black text-yellow-600">
            {{ $pendingCount + $pendingDeleteCount }}
          </span>
        </a>
      @endif
      <a href="{{ route('admin.articles.create') }}"
         class="rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-brand-700 transition">
        + Add Article
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-xl bg-green-50 border border-green-200 px-5 py-3 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-6 flex h-14 items-center rounded-xl border border-gray-200 px-5 text-gray-400 shadow-sm">
    🔍 <span class="ml-3">Search Articles by title, category, or author...</span>
  </div>

  <div class="mt-6 grid grid-cols-[60px_120px_1fr_130px_110px_150px_140px_180px] gap-2 rounded-xl bg-tablehead px-4 py-3 text-sm font-bold text-gray-800">
    <div>No</div><div>Thumbnail</div><div>Judul Artikel</div><div>Kategori</div><div>Penulis</div><div>Tanggal</div><div>Status</div><div>Action</div>
  </div>

  <div class="mt-3 space-y-3">
    @forelse($articles as $i => $article)
    <div class="grid grid-cols-[60px_120px_1fr_130px_110px_150px_140px_180px] items-center gap-2 rounded-2xl bg-white px-4 py-4 shadow-[0_2px_10px_rgba(0,0,0,0.06)]">
      <div class="text-lg font-bold">{{ $i + 1 + ($articles->currentPage() - 1) * $articles->perPage() }}</div>
      <div>
        @if($article->image)
          <img src="{{ $article->image_url }}" class="h-[80px] w-[100px] rounded object-cover">
        @else
          <div class="h-[80px] w-[100px] rounded bg-gray-200 flex items-center justify-center text-xs text-gray-500">No Image</div>
        @endif
      </div>
      <div class="pr-4">
        <p class="text-sm font-bold leading-snug text-gray-900">{{ $article->title }}</p>
        @if($article->user)
          <p class="mt-1 text-xs text-gray-400">oleh {{ $article->user->name }} ({{ ucfirst($article->user->role) }})</p>
        @endif
      </div>
      <div><span class="rounded-full bg-badge-cat px-3 py-1 text-xs font-semibold text-white">{{ $article->category->name ?? 'Uncategorized' }}</span></div>
      <div class="text-sm font-bold">{{ $article->writer }}</div>
      <div class="text-sm font-bold">{{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }}</div>
      <div>
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
        @endphp
        <span class="rounded-md {{ $sc }} px-3 py-1 text-xs font-semibold text-white">{{ $sl }}</span>
      </div>
      <div class="flex flex-wrap gap-1.5">
        <a href="{{ route('admin.articles.edit', $article) }}"
           class="rounded-md bg-edit px-3 py-1.5 text-xs font-bold text-black">Edit</a>

        @if($article->status === 'pending')
          <form action="{{ route('admin.articles.approve', $article) }}" method="POST" class="inline">
            @csrf @method('PATCH')
            <button class="rounded-md bg-green-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-green-600">✅ Acc</button>
          </form>
          <form action="{{ route('admin.articles.reject', $article) }}" method="POST" class="inline">
            @csrf @method('PATCH')
            <button class="rounded-md bg-orange-400 px-3 py-1.5 text-xs font-bold text-white hover:bg-orange-500">❌ Tolak</button>
          </form>
        @endif

        @if($article->status === 'pending_delete')
          <form action="{{ route('admin.articles.approveDelete', $article) }}" method="POST" class="inline"
                onsubmit="return confirm('Hapus artikel ini?')">
            @csrf @method('DELETE')
            <button class="rounded-md bg-red-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-600">🗑 Hapus</button>
          </form>
          <form action="{{ route('admin.articles.rejectDelete', $article) }}" method="POST" class="inline">
            @csrf @method('PATCH')
            <button class="rounded-md bg-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-300">↩ Batal</button>
          </form>
        @else
          <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline"
                onsubmit="return confirm('Hapus artikel ini?')">
            @csrf @method('DELETE')
            <button class="rounded-md bg-danger px-3 py-1.5 text-xs font-bold text-white">Delete</button>
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
</main>
</x-layouts.app>
