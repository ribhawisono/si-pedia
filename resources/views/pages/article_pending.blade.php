<x-layouts.admin title="Artikel Pending — SI-Pedia" section="pending">
<main class="mx-auto max-w-[1200px] px-4 sm:px-8 py-10">

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="page-title">Pending Artikel</h1>
      <p class="mt-2 text-gray-500">Artikel yang menunggu persetujuan dan permintaan hapus dari pengguna.</p>
    </div>
    <a href="{{ route('admin.articles.index') }}"
       class="self-start rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-200 transition">
      ← Kembali ke Semua Artikel
    </a>
  </div>

  @if(session('success'))
    <div class="mt-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-10">
    <div class="flex items-center gap-3 mb-5">
      <h2 class="text-base font-bold text-gray-900">Menunggu Persetujuan</h2>
      <span class="rounded-full bg-yellow-400 px-3 py-1 text-sm font-bold text-white">
        {{ $pending->total() }} artikel
      </span>
    </div>
    <p class="mb-5 text-xs text-gray-400">
      ℹ️ Approve/Tolak dilakukan setelah membaca isi lengkap artikel di halaman Review — klik "Review Isi" untuk memutuskan.
    </p>

    @forelse($pending as $article)
    {{-- flex-wrap: action column drops to its own row on narrow screens
         instead of squeezing the title/description into a sliver. --}}
    <a href="{{ route('admin.articles.preview', $article) }}"
       class="mb-4 flex flex-wrap items-start gap-4 rounded-2xl bg-white border border-yellow-200 shadow-sm px-4 sm:px-6 py-5 hover:border-yellow-400 hover:shadow-md transition">
      @if($article->image)
        <img src="{{ $article->image_url }}" class="h-20 w-24 rounded-lg object-cover flex-shrink-0">
      @else
        <div class="h-20 w-24 rounded-lg bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📄</div>
      @endif

      <div class="flex-1 min-w-[160px]">
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

      <div class="w-full sm:w-auto flex sm:flex-col items-center gap-2 border-t border-gray-100 pt-3 sm:border-t-0 sm:pt-0">
        <span class="flex-1 sm:flex-initial flex items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white">
          👁 Review Isi
        </span>
        <span class="hidden sm:block text-[11px] text-gray-400">Approve / Tolak di sini</span>
      </div>
    </a>
    @empty
    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-10 text-center text-gray-400">
      <p class="text-3xl mb-2">🎉</p>
      <p class="font-semibold">Tidak ada artikel yang menunggu persetujuan.</p>
    </div>
    @endforelse

    <div class="mt-4">{{ $pending->links() }}</div>
  </div>

  <div class="mt-12">
    <div class="flex items-center gap-3 mb-5">
      <h2 class="text-base font-bold text-gray-900">Permintaan Hapus</h2>
      <span class="rounded-full bg-red-500 px-3 py-1 text-sm font-bold text-white">
        {{ $pendingDelete->total() }} artikel
      </span>
    </div>

    @forelse($pendingDelete as $article)
    <div class="mb-4 flex flex-wrap items-start gap-4 rounded-2xl bg-white border border-red-200 shadow-sm px-4 sm:px-6 py-5">
      @if($article->image)
        <img src="{{ $article->image_url }}" class="h-20 w-24 rounded-lg object-cover flex-shrink-0">
      @else
        <div class="h-20 w-24 rounded-lg bg-gray-100 flex items-center justify-center text-2xl flex-shrink-0">📄</div>
      @endif

      <div class="flex-1 min-w-[160px]">
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

      <div class="w-full sm:w-auto flex gap-2 border-t border-gray-100 pt-3 sm:border-t-0 sm:pt-0 sm:flex-col">
        <form action="{{ route('admin.articles.approveDelete', $article) }}" method="POST" class="flex-1 sm:flex-initial"
              onsubmit="return confirm('Hapus artikel ini secara permanen?')">
          @csrf @method('DELETE')
          <button type="submit"
                  class="w-full rounded-xl bg-red-500 px-5 py-2 text-sm font-bold text-white hover:bg-red-600 transition">
            🗑 Hapus Sekarang
          </button>
        </form>
        <form action="{{ route('admin.articles.rejectDelete', $article) }}" method="POST" class="flex-1 sm:flex-initial">
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
      <p class="text-3xl mb-2">✨</p>
      <p class="font-semibold">Tidak ada permintaan hapus artikel.</p>
    </div>
    @endforelse

    <div class="mt-4">{{ $pendingDelete->links() }}</div>
  </div>

</main>
</x-layouts.admin>
