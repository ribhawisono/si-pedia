<x-layouts.app title="Artikel Saya — SI-Pedia">
<main class="mx-auto max-w-[1100px] px-4 sm:px-8 py-10">

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h1 class="page-title text-gray-900">Artikel Saya</h1>
      <p class="page-subtitle">Kelola semua artikel yang pernah kamu tulis.</p>
    </div>
    <a href="{{ route('articles.create') }}"
       class="self-start rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow hover:bg-brand-700 transition">
      ✏️ Tulis Artikel Baru
    </a>
  </div>

  @if(session('success'))
    <div class="mt-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mt-6 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm font-semibold text-red-700">
      ⚠️ {{ session('error') }}
    </div>
  @endif

  {{-- Status legend --}}
  <div class="mt-6 flex flex-wrap gap-2 sm:gap-3 text-xs font-semibold">
    <span class="rounded-full bg-green-500 px-3 py-1 text-white">Active = Publik</span>
    <span class="rounded-full bg-yellow-400 px-3 py-1 text-white">Pending = Menunggu Persetujuan</span>
    <span class="rounded-full bg-gray-400 px-3 py-1 text-white">Draft = Tersimpan</span>
    <span class="rounded-full bg-red-500 px-3 py-1 text-white">Pending Delete = Request Hapus Dikirim</span>
    <span class="rounded-full bg-purple-500 px-3 py-1 text-white">Takedown = Perlu Diperbaiki</span>
    <span class="rounded-full bg-gray-700 px-3 py-1 text-white">Dihapus = Dihapus Admin</span>
  </div>

  <div class="mt-6 space-y-4">
    @forelse($articles as $article)

      {{-- Article deleted (Hapus) by admin/self-request: writer is only told
           it was deleted — no edit, no content, nothing to view. --}}
      @if($article->trashed() && $article->trashed_reason !== 'takedown')
      <div class="rounded-2xl bg-gray-50 border border-gray-200 px-4 sm:px-6 py-5 flex flex-wrap items-center gap-4 opacity-75">
        <div class="h-20 w-24 rounded-lg bg-gray-200 flex items-center justify-center text-2xl flex-shrink-0">🗑</div>
        <div class="flex-1 min-w-[140px]">
          <h2 class="text-lg font-extrabold text-gray-500">(Artikel telah dihapus)</h2>
          <p class="mt-1 text-sm text-gray-400">Dihapus pada {{ $article->deleted_at->translatedFormat('j F Y') }}</p>
        </div>
        <span class="rounded-full bg-gray-700 px-4 py-1 text-xs font-bold text-white flex-shrink-0">Dihapus</span>
      </div>

      {{-- Article takedown: fully editable, shows admin's note --}}
      @elseif($article->trashed() && $article->trashed_reason === 'takedown')
      <div class="rounded-2xl bg-white shadow-sm border border-purple-200 px-4 sm:px-6 py-5">
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex-shrink-0">
            @if($article->image)
              <img src="{{ $article->image_url }}" class="h-20 w-24 rounded-lg object-cover">
            @else
              <div class="h-20 w-24 rounded-lg bg-gray-100 flex items-center justify-center text-2xl">📄</div>
            @endif
          </div>
          <div class="flex-1 min-w-[140px]">
            <h2 class="text-lg font-extrabold text-gray-900 truncate">{{ $article->title }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $article->category->name ?? 'Tanpa Kategori' }}</p>
          </div>
          <div class="w-full sm:w-auto flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-purple-500 px-4 py-1 text-xs font-bold text-white flex-shrink-0">Takedown</span>
            <a href="{{ route('articles.edit', $article) }}"
               class="flex-1 sm:flex-initial text-center rounded-lg bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100 transition">
              ✏️ Edit &amp; Perbaiki
            </a>
            <a href="{{ route('articles.revisions', $article) }}"
               class="flex-1 sm:flex-initial text-center rounded-lg border border-gray-200 px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
              📜 Revisi
            </a>
            {{-- Takedown/reject sudah pasti belum pernah live lagi (statusnya
                 balik ke draft di belakang layar) -> boleh dihapus langsung
                 tanpa approval admin, sama seperti draft biasa. --}}
            <form action="{{ route('articles.destroy', $article) }}" method="POST" class="flex-1 sm:flex-initial"
                  onsubmit="return confirm('Hapus artikel ini? Tindakan ini langsung memindahkan ke Trash tanpa perlu persetujuan admin.')">
              @csrf @method('DELETE')
              <button type="submit" class="w-full text-center rounded-lg bg-red-50 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition">
                🗑 Hapus
              </button>
            </form>
          </div>
        </div>
        @if($article->rejection_note)
        <div class="mt-4 rounded-xl bg-purple-50 border border-purple-200 px-4 py-3">
          <p class="text-xs font-bold text-purple-700 mb-1">⬇ Ditakedown admin — perlu diperbaiki:</p>
          <p class="text-sm text-purple-700">{{ $article->rejection_note }}</p>
        </div>
        @endif
      </div>

      @else
      {{-- Normal (non-trashed) article --}}
      <div class="rounded-2xl bg-white shadow-sm border border-gray-100 px-4 sm:px-6 py-5">
       <div class="flex flex-wrap items-center gap-4">

        <div class="flex-shrink-0">
          @if($article->image)
            <img src="{{ $article->image_url }}" class="h-20 w-24 rounded-lg object-cover">
          @else
            <div class="h-20 w-24 rounded-lg bg-gray-100 flex items-center justify-center text-2xl">📄</div>
          @endif
        </div>

        <div class="flex-1 min-w-[140px]">
          <h2 class="text-lg font-extrabold text-gray-900 truncate">{{ $article->title }}</h2>
          <p class="mt-1 text-sm text-gray-500">
            {{ $article->category->name ?? 'Tanpa Kategori' }} ·
            {{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }}
          </p>
          @if($article->status === 'pending_delete')
            <p class="mt-1 text-xs text-red-500 font-semibold">⏳ Permintaan hapus sedang menunggu keputusan admin.</p>
          @elseif($article->status === 'pending')
            <p class="mt-1 text-xs text-yellow-600 font-semibold">⏳ Artikel sedang menunggu persetujuan admin untuk dipublikasikan.</p>
          @elseif($article->status === 'active' && $pendingEditArticleIds->contains($article->id))
            <p class="mt-1 text-xs text-blue-600 font-semibold">⏳ Ada usulan perubahan menunggu persetujuan admin. Artikel yang tayang masih versi lama.</p>
          @endif
        </div>

        {{-- Badge + actions: wraps to its own full-width row on narrow
             screens (flex-wrap on the parent) instead of squeezing the
             title/date column into nothing. --}}
        <div class="w-full sm:w-auto flex flex-wrap items-center gap-2 border-t border-gray-100 pt-3 mt-1 sm:border-t-0 sm:pt-0 sm:mt-0">
          @php
            $badgeClass = match($article->status) {
              'active'         => 'bg-green-500',
              'pending'        => 'bg-yellow-400',
              'pending_delete' => 'bg-red-500',
              default          => 'bg-gray-400',
            };
            $badgeLabel = match($article->status) {
              'active'         => 'Active',
              'pending'        => 'Pending',
              'pending_delete' => 'Pending Delete',
              default          => 'Draft',
            };
          @endphp
          <span class="rounded-full {{ $badgeClass }} px-4 py-1 text-xs font-bold text-white">{{ $badgeLabel }}</span>
          @if($article->status === 'active' && $pendingEditArticleIds->contains($article->id))
          <span class="rounded-full bg-blue-100 px-3 py-1 text-[11px] font-bold text-blue-700">✏️ Revisi Menunggu Review</span>
          @endif

          @if($article->status === 'active')
            <a href="{{ route('articles.show', $article->slug) }}"
               class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-200 transition">
              👁 Lihat
            </a>
          @endif

          {{-- Edit: sebelumnya diblokir total untuk artikel Active. Sekarang
               dibuka — mengedit artikel live TIDAK langsung mengubah yang
               tayang, melainkan jadi usulan yang perlu di-approve admin dulu
               (lihat ArticleController::update()/submitPendingEdit). --}}
          <a href="{{ route('articles.edit', $article) }}"
             class="rounded-lg bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100 transition">
            ✏️ Edit
          </a>

          {{-- Revisi: sebelumnya cuma bisa diakses lewat halaman Edit, jadi
               untuk artikel yang sudah Active (edit-nya diblokir) penulis
               tidak pernah punya jalan ke riwayat revisi. Sekarang selalu
               ada di sini, apapun statusnya. --}}
          <a href="{{ route('articles.revisions', $article) }}"
             class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
            📜 Revisi
          </a>

          @if($article->status === 'active')
            {{-- Sudah live: hapus tetap lewat approval admin. --}}
            @if($article->status !== 'pending_delete')
            <form action="{{ route('articles.requestDelete', $article) }}" method="POST"
                  onsubmit="return confirm('Kirim permintaan hapus artikel ini ke admin?')">
              @csrf @method('PATCH')
              <button type="submit"
                      class="rounded-lg bg-red-50 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition">
                🗑 Request Hapus
              </button>
            </form>
            @endif
          @elseif($article->status === 'draft')
            {{-- Draft (termasuk hasil reject) belum pernah tayang -> hapus
                 langsung tanpa approval admin. --}}
            <form action="{{ route('articles.destroy', $article) }}" method="POST"
                  onsubmit="return confirm('Hapus artikel draft ini? Tidak perlu persetujuan admin.')">
              @csrf @method('DELETE')
              <button type="submit"
                      class="rounded-lg bg-red-50 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition">
                🗑 Hapus
              </button>
            </form>
          @endif
        </div>
       </div>

        @if($article->status === 'draft' && $article->rejection_note)
        <div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
          <p class="text-xs font-bold text-red-700 mb-1">⚠️ Ditolak admin — perlu diperbaiki:</p>
          <p class="text-sm text-red-700">{{ $article->rejection_note }}</p>
        </div>
        @endif
      </div>
      @endif

    @empty
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-16 text-center">
      <p class="text-5xl mb-4">📝</p>
      <p class="text-xl font-bold text-gray-700">Belum ada artikel</p>
      <p class="mt-2 text-gray-400">Mulai tulis artikel pertamamu sekarang!</p>
      <a href="{{ route('articles.create') }}"
         class="mt-6 inline-block rounded-xl bg-brand-600 px-8 py-3 text-sm font-bold text-white hover:bg-brand-700 transition">
        ✏️ Tulis Sekarang
      </a>
    </div>
    @endforelse
  </div>

  <div class="mt-6">{{ $articles->links() }}</div>
</main>
</x-layouts.app>
