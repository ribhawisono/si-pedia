@php
  $isAdmin    = $isAdmin ?? (auth()->user()->role === 'admin');
  $layoutName = $isAdmin ? 'layouts.admin' : 'layouts.app';
  $backRoute  = $isAdmin ? route('admin.articles.edit', $article) : route('articles.edit', $article);
  $pendingEdit = $pendingEdit ?? $revisions->firstWhere('status', 'pending_edit');
@endphp
<x-dynamic-component :component="$layoutName" title="Riwayat Revisi — SI-Pedia" section="articles">

<div class="{{ $isAdmin ? '' : 'mx-auto max-w-4xl px-4 sm:px-8 py-8' }}">

<div class="mb-5 flex items-center gap-3">
  <a href="{{ $backRoute }}" class="rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50 transition" aria-label="Kembali">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
  </a>
  <div>
    <h2 class="text-xl font-extrabold text-gray-900">Riwayat Revisi</h2>
    <p class="text-sm text-gray-500 truncate max-w-[400px]">{{ $article->title }}</p>
  </div>
</div>

{{-- Panel review: HANYA muncul untuk admin, saat ada usulan perubahan
     (pending_edit) menunggu keputusan. Penulis tetap melihat status "Menunggu
     Review" di daftar di bawah, tapi tombol Setujui/Tolak hanya untuk admin. --}}
@if($isAdmin && $pendingEdit)
<div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-4 sm:p-5">
  <p class="mb-1 text-sm font-bold text-blue-800">✏️ Ada usulan perubahan menunggu review</p>
  <p class="mb-3 text-xs text-blue-600">Diusulkan oleh {{ $pendingEdit->user->name ?? 'penulis' }} · {{ $pendingEdit->created_at->translatedFormat('j M Y, H:i') }}. Artikel yang tayang belum berubah.</p>

  <div class="mb-4 grid gap-3 sm:grid-cols-2">
    <div class="rounded-lg border border-red-100 bg-white p-3">
      <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-red-500">Sekarang Tayang</p>
      @if($article->title !== $pendingEdit->title)
      <p class="mb-1 text-xs font-semibold text-gray-700">{{ $article->title }}</p>
      @endif
      <p class="text-xs leading-relaxed text-gray-600 whitespace-pre-line">{{ Str::limit(strip_tags($article->content), 500) }}</p>
    </div>
    <div class="rounded-lg border border-green-100 bg-white p-3">
      <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-green-600">Usulan Baru</p>
      @if($article->title !== $pendingEdit->title)
      <p class="mb-1 text-xs font-semibold text-gray-900">{{ $pendingEdit->title }}</p>
      @endif
      <p class="text-xs leading-relaxed text-gray-700 whitespace-pre-line">{{ Str::limit(strip_tags($pendingEdit->content), 500) }}</p>
    </div>
  </div>

  <div class="flex flex-col sm:flex-row gap-3">
    <form action="{{ route('admin.articles.approveEdit', $article) }}" method="POST" class="sm:w-48">
      @csrf @method('PATCH')
      <button type="submit" class="w-full rounded-xl bg-green-500 py-2.5 text-sm font-bold text-white hover:bg-green-600 transition">
        ✅ Setujui &amp; Tayangkan
      </button>
    </form>
    <form action="{{ route('admin.articles.rejectEdit', $article) }}" method="POST" class="flex-1 flex flex-col sm:flex-row gap-2">
      @csrf @method('PATCH')
      <input type="text" name="note" maxlength="1000" placeholder="Catatan penolakan (opsional)..."
             class="flex-1 rounded-xl border border-blue-200 px-3 py-2 text-sm text-gray-700 focus:border-red-400 focus:ring-0">
      <button type="submit" class="rounded-xl bg-red-100 px-5 py-2.5 text-sm font-bold text-red-600 hover:bg-red-200 transition whitespace-nowrap">
        ❌ Tolak Usulan
      </button>
    </form>
  </div>
</div>
@elseif(!$isAdmin && $pendingEdit)
<div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
  ⏳ Usulan perubahanmu sedang menunggu review admin. Artikel yang tayang masih versi lama.
</div>
@endif

{{-- Catatan: halaman ini HANYA bisa diakses admin atau penulis artikel
     sendiri (lihat ArticleController::revisions()). Detail before/after di
     bawah TIDAK pernah dipanggil dari halaman publik (articles.show), jadi
     isi revisi lama tidak bocor ke pembaca umum. --}}
<div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
  @forelse($revisions as $i => $rev)
  @php
    // Revisi sebelumnya ($prev) dipakai untuk membandingkan before/after.
    // Urutan array $revisions terbaru duluan (lihat Article::revisions()
    // yang pakai ->latest()), jadi "sebelumnya" ada di index setelahnya.
    $prev = $revisions[$i + 1] ?? null;
    $badgeClass = match($rev->status) {
      'active'       => 'bg-green-100 text-green-700',
      'pending_edit' => 'bg-blue-100 text-blue-700',
      'rejected'     => 'bg-red-100 text-red-600',
      'draft'        => 'bg-gray-100 text-gray-600',
      default        => 'bg-yellow-100 text-yellow-700',
    };
    $badgeLabel = match($rev->status) {
      'active'       => 'Live',
      'pending_edit' => 'Menunggu Review',
      'rejected'     => 'Ditolak',
      default        => ucfirst($rev->status),
    };
  @endphp
  <div class="border-b border-gray-100 last:border-0">
    <button type="button" onclick="document.getElementById('rev-diff-{{ $rev->id }}').classList.toggle('hidden'); this.querySelector('.chev').classList.toggle('rotate-180')"
            class="flex w-full items-start gap-4 p-5 hover:bg-gray-50 transition-colors text-left">
      <div class="flex-shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-brand-600/10 text-xs font-bold text-brand-700">
        {{ $revisions->count() - $i }}
      </div>
      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-3 mb-1">
          <span class="text-sm font-bold text-gray-900">{{ $rev->title }}</span>
          <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $badgeClass }}">{{ $badgeLabel }}</span>
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
          @if($prev)
          <span class="ml-1 inline-flex items-center gap-1 font-semibold text-brand-600">
            Lihat perubahan
            <svg class="chev h-3 w-3 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
          </span>
          @endif
        </div>
      </div>
      <div class="text-xs text-gray-400 text-right flex-shrink-0">
        {{ number_format(str_word_count(strip_tags($rev->content))) }} kata
      </div>
    </button>

    @if($prev)
    <div id="rev-diff-{{ $rev->id }}" class="hidden px-5 pb-5">
      <div class="grid gap-3 sm:grid-cols-2">
        <div class="rounded-lg border border-red-100 bg-red-50/50 p-3">
          <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-red-500">Sebelum ({{ $prev->created_at->translatedFormat('j M Y, H:i') }})</p>
          @if($prev->title !== $rev->title)
          <p class="mb-1 text-xs font-semibold text-gray-700 line-through decoration-red-400">{{ $prev->title }}</p>
          @endif
          <p class="text-xs leading-relaxed text-gray-600 whitespace-pre-line">{{ Str::limit(strip_tags($prev->content), 500) }}</p>
        </div>
        <div class="rounded-lg border border-green-100 bg-green-50/50 p-3">
          <p class="mb-2 text-[10px] font-bold uppercase tracking-wide text-green-600">Sesudah ({{ $rev->created_at->translatedFormat('j M Y, H:i') }})</p>
          @if($prev->title !== $rev->title)
          <p class="mb-1 text-xs font-semibold text-gray-900">{{ $rev->title }}</p>
          @endif
          <p class="text-xs leading-relaxed text-gray-700 whitespace-pre-line">{{ Str::limit(strip_tags($rev->content), 500) }}</p>
        </div>
      </div>
    </div>
    @endif
  </div>
  @empty
  <div class="py-12 text-center text-sm text-gray-400">Belum ada riwayat revisi.</div>
  @endforelse
</div>

</div>
</x-dynamic-component>
