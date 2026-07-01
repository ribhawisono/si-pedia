<x-layouts.app title="Pending Artikel — SI-Pedia">
<main class="mx-auto max-w-[1200px] px-8 py-10">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-5xl font-extrabold">Pending Artikel</h1>
      <p class="mt-2 text-gray-500">Artikel yang menunggu persetujuan dan permintaan hapus dari pengguna.</p>
    </div>
    <a href="{{ route('admin.articles.index') }}"
       class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-200 transition">
      ← Kembali ke Semua Artikel
    </a>
  </div>

  @if(session('success'))
    <div class="mt-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif

  {{-- ===== SECTION 1: PENDING APPROVAL ===== --}}
  <div class="mt-10">
    <div class="flex items-center gap-3 mb-5">
      <h2 class="text-3xl font-extrabold">Menunggu Persetujuan</h2>
      <span class="rounded-full bg-yellow-400 px-3 py-1 text-sm font-bold text-white">
        {{ $pending->total() }} artikel
      </span>
    </div>

    @forelse($pending as $article)
    <div class="mb-4 rounded-2xl bg-white border border-yellow-200 shadow-sm px-6 py-5 flex items-center gap-5">
      @if($article->image)
        <img src="{{ Storage::url($article->image) }}" class="h-20 w-24 rounded-lg object-cover flex-shrink-0">
      @else
        <div class="h-20 w-24 rounded-lg bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📄</div>
      @endif

      <div class="flex-1 min-w-0">
        <h3 class="text-lg font-extrabold text-gray-900 truncate">{{ $article->title }}</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ $article->category->name ?? '-' }} ·
          Ditulis oleh <span class="font-semibold">{{ $article->user->name ?? $article->writer }}</span>
          ({{ ucfirst($article->user->role ?? 'user') }}) ·
          {{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }}
        </p>
        <p class="mt-2 text-sm text-gray-600 line-clamp-2">
          {{ Str::limit(strip_tags($article->content), 150) }}
        </p>
      </div>

      <div class="flex-shrink-0 flex flex-col gap-2">
        <form action="{{ route('admin.articles.approve', $article) }}" method="POST">
          @csrf @method('PATCH')
          <button type="submit"
                  class="w-full rounded-xl bg-green-500 px-5 py-2 text-sm font-bold text-white hover:bg-green-600 transition">
            ✅ Approve
          </button>
        </form>
        <form action="{{ route('admin.articles.reject', $article) }}" method="POST"
              onsubmit="return confirm('Tolak dan kembalikan ke draft?')">
          @csrf @method('PATCH')
          <button type="submit"
                  class="w-full rounded-xl bg-red-100 px-5 py-2 text-sm font-bold text-red-600 hover:bg-red-200 transition">
            ❌ Tolak
          </button>
        </form>
        <a href="{{ route('admin.articles.edit', $article) }}"
           class="block text-center rounded-xl bg-gray-100 px-5 py-2 text-sm font-bold text-gray-600 hover:bg-gray-200 transition">
          ✏️ Edit
        </a>
      </div>
    </div>
    @empty
    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-10 text-center text-gray-400">
      <p class="text-4xl mb-3">🎉</p>
      <p class="font-semibold">Tidak ada artikel yang menunggu persetujuan.</p>
    </div>
    @endforelse

    <div class="mt-4">{{ $pending->links() }}</div>
  </div>

  {{-- ===== SECTION 2: REQUEST HAPUS ===== --}}
  <div class="mt-12">
    <div class="flex items-center gap-3 mb-5">
      <h2 class="text-3xl font-extrabold">Permintaan Hapus</h2>
      <span class="rounded-full bg-red-500 px-3 py-1 text-sm font-bold text-white">
        {{ $pendingDelete->total() }} artikel
      </span>
    </div>

    @forelse($pendingDelete as $article)
    <div class="mb-4 rounded-2xl bg-white border border-red-200 shadow-sm px-6 py-5 flex items-center gap-5">
      @if($article->image)
        <img src="{{ Storage::url($article->image) }}" class="h-20 w-24 rounded-lg object-cover flex-shrink-0">
      @else
        <div class="h-20 w-24 rounded-lg bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📄</div>
      @endif

      <div class="flex-1 min-w-0">
        <h3 class="text-lg font-extrabold text-gray-900 truncate">{{ $article->title }}</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ $article->category->name ?? '-' }} ·
          Milik <span class="font-semibold">{{ $article->user->name ?? $article->writer }}</span>
          ({{ ucfirst($article->user->role ?? 'user') }}) ·
          {{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }}
        </p>
        <p class="mt-2 text-sm text-red-500 font-semibold">
          ⚠️ Pengguna meminta agar artikel ini dihapus.
        </p>
      </div>

      <div class="flex-shrink-0 flex flex-col gap-2">
        <form action="{{ route('admin.articles.approveDelete', $article) }}" method="POST"
              onsubmit="return confirm('Hapus artikel ini secara permanen?')">
          @csrf @method('DELETE')
          <button type="submit"
                  class="w-full rounded-xl bg-red-500 px-5 py-2 text-sm font-bold text-white hover:bg-red-600 transition">
            🗑 Hapus Sekarang
          </button>
        </form>
        <form action="{{ route('admin.articles.rejectDelete', $article) }}" method="POST">
          @csrf @method('PATCH')
          <button type="submit"
                  class="w-full rounded-xl bg-gray-100 px-5 py-2 text-sm font-bold text-gray-600 hover:bg-gray-200 transition">
            ↩ Batalkan Request
          </button>
        </form>
      </div>
    </div>
    @empty
    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-10 text-center text-gray-400">
      <p class="text-4xl mb-3">✨</p>
      <p class="font-semibold">Tidak ada permintaan hapus artikel.</p>
    </div>
    @endforelse

    <div class="mt-4">{{ $pendingDelete->links() }}</div>
  </div>

</main>
</x-layouts.app>
