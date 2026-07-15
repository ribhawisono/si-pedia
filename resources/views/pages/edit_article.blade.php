@php
  $isAdmin     = $isAdmin ?? (auth()->user()->role === 'admin');
  $pendingEdit = $pendingEdit ?? null;
  $backRoute   = $isAdmin ? route('admin.articles.index') : route('articles.my');
  $storeRoute  = $isAdmin
    ? ($mode === 'create' ? route('admin.articles.store') : route('admin.articles.update', $article))
    : ($mode === 'create' ? route('articles.store')       : route('articles.update', $article));
  $autosaveKey = 'article_draft_' . ($article->id ?? 'new');
  // Artikel live (active) yang diedit non-admin: form ini jadi "usulan
  // perubahan", jadi prefill dari revisi pending kalau sudah ada satu
  // (lanjutkan draft usulan sebelumnya), bukan dari konten yang tayang.
  $isLiveEditFlow = !$isAdmin && $mode === 'edit' && $article->status === 'active' && !$article->trashed();
  // localStorage key dipakai preview-link JS di bawah (harus sama persis
  // dengan yang dibaca article_preview.blade.php).
  $previewDraftKey = 'sipedia_preview_draft_' . ($article->id ?? '');
@endphp
@php
  $layoutName  = $isAdmin ? 'layouts.admin' : 'layouts.app';
  $layoutTitle = $isAdmin
      ? ($mode === 'create' ? 'Tambah Artikel' : 'Edit Artikel')
      : ($mode === 'create' ? 'Tulis Artikel — SI-Pedia' : 'Edit Artikel — SI-Pedia');
@endphp
<x-dynamic-component :component="$layoutName" :title="$layoutTitle" section="articles">

{{-- Quill rich text editor (CDN) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">

<div class="{{ $isAdmin ? '' : 'mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 py-6' }}">
  <div class="mb-5 flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ $backRoute }}" class="rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-brand-600" aria-label="Kembali">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
      </a>
      <div>
        <h1 class="text-xl font-extrabold text-gray-900">
          {{ $mode === 'create' ? 'Tulis Artikel Baru' : ($isLiveEditFlow ? 'Usulkan Perubahan' : 'Edit Artikel') }}
        </h1>
        <p id="autosave-indicator" class="mt-0.5 hidden text-xs font-semibold text-green-600"></p>
      </div>
    </div>
    @if($mode === 'edit' && isset($article->id))
    <div class="flex gap-2">
      <a href="{{ $isAdmin ? route('admin.articles.revisions', $article) : route('articles.revisions', $article) }}"
         class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
        📜 Revisi
      </a>
      {{-- Preview "hidup": sebelum navigasi, tulis draft form saat ini
           (belum disimpan) ke localStorage. article_preview.blade.php baca
           key yang sama dan menimpa tampilan JIKA ada; kalau tidak ada
           (belum pernah diedit / sudah dibersihkan setelah save), preview
           tetap menampilkan versi tersimpan/live seperti biasa. --}}
      <a href="{{ $isAdmin ? route('admin.articles.preview', $article) : route('articles.preview', $article) }}" target="_blank"
         id="preview-link" data-preview-key="{{ $previewDraftKey }}"
         class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">
        👁 Preview
      </a>
    </div>
    @endif
  </div>

  @if($isLiveEditFlow)
  <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
    @if($pendingEdit)
      ⏳ Kamu sedang melanjutkan usulan perubahan yang <strong>belum disetujui admin</strong>. Artikel yang tayang masih versi lama sampai disetujui.
    @else
      ℹ️ Artikel ini sudah tayang. Perubahan yang kamu simpan di sini <strong>tidak langsung tayang</strong> — akan dikirim sebagai usulan dan menunggu persetujuan admin dulu.
    @endif
  </div>
  @endif

  @if($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3" role="alert">
      <ul class="text-sm text-red-600 space-y-1">
        @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form id="article-form" action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data"
        data-validate data-autosave="{{ $autosaveKey }}">
  @csrf
  @if($mode === 'edit') @method('PUT') @endif

  <div class="grid gap-6 lg:grid-cols-[1fr_300px]">

    {{-- ── Main Content Column ────────────────────── --}}
    <div class="space-y-5">

      {{-- Title --}}
      <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <label for="article-title-input" class="mb-2 block text-sm font-bold text-gray-700">
          Judul Artikel <span class="text-red-500" aria-hidden="true">*</span>
        </label>
        <input id="article-title-input" type="text" name="title"
               value="{{ old('title', $pendingEdit->title ?? $article->title) }}" required
               placeholder="Masukkan judul artikel..."
               class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-base font-semibold text-gray-900 focus:border-brand-600 focus:outline-none focus:ring-0 transition"
               aria-required="true">
        <p class="mt-1.5 text-xs text-gray-400">
          URL: <span class="font-mono text-brand-600">/articles/<span id="slug-preview">{{ $article->slug ?? 'judul-artikel' }}</span></span>
        </p>
      </div>

      {{-- Category + Writer --}}
      <div class="grid gap-4 sm:grid-cols-2 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div>
          <label for="category_id" class="mb-2 block text-sm font-bold text-gray-700">Kategori <span class="text-red-500" aria-hidden="true">*</span></label>
          <select id="category_id" name="category_id" required
                  class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:outline-none focus:ring-0">
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected(old('category_id', $article->category_id) == $cat->id)>{{ $cat->name }}</option>
            @endforeach
          </select>
          @if($isLiveEditFlow)
          <p class="mt-1.5 text-[11px] text-gray-400">Kategori & tag tidak termasuk dalam usulan perubahan — hanya judul & isi.</p>
          @endif
        </div>
        @if($isAdmin)
        <div>
          <label for="writer" class="mb-2 block text-sm font-bold text-gray-700">Penulis <span class="text-red-500" aria-hidden="true">*</span></label>
          <input id="writer" type="text" name="writer"
                 value="{{ old('writer', $article->writer ?? auth()->user()->name) }}" required
                 class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:outline-none focus:ring-0">
        </div>
        @else
        <div>
          <p class="mb-2 text-sm font-bold text-gray-700">Penulis</p>
          <div class="flex items-center gap-2 rounded-xl border-2 border-gray-100 bg-gray-50 px-4 py-2.5">
            <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-6 w-6 rounded-full object-cover" aria-hidden="true">
            <span class="text-sm font-semibold text-gray-600">{{ auth()->user()->name }}</span>
          </div>
        </div>
        @endif
      </div>

      {{-- Tags --}}
      <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <label for="tags-input" class="mb-2 block text-sm font-bold text-gray-700">
          Tags
          <span class="font-normal text-gray-400">(pisahkan dengan koma)</span>
        </label>
        <input id="tags-input" type="text" name="tags"
               value="{{ old('tags', isset($article->tags) ? $article->tags->pluck('name')->implode(', ') : '') }}"
               placeholder="Contoh: AI, Machine Learning, Akademik"
               class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-brand-600 focus:outline-none focus:ring-0 transition">
        <p class="mt-1 text-xs text-gray-400">Tag membantu pembaca menemukan artikel serupa.</p>
      </div>

      {{-- Thumbnail --}}
      <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <label class="mb-3 block text-sm font-bold text-gray-700">Thumbnail Artikel</label>
        <div class="flex flex-wrap items-start gap-4">
          @if($article->image_url)
          <img src="{{ $article->image_url }}" alt="Thumbnail saat ini" data-preview
               class="h-28 w-40 rounded-xl object-cover shadow-sm border border-gray-200">
          @endif
          <label class="cursor-pointer">
            <div class="flex h-28 w-40 items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 hover:border-brand-300 hover:bg-brand-50 transition text-center p-3">
              <div>
                <svg class="mx-auto h-8 w-8 text-gray-300 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                <p class="text-xs font-semibold text-gray-500">Upload gambar</p>
                <p class="text-[10px] text-gray-400">JPG, PNG, WEBP, max 10MB</p>
              </div>
            </div>
            <input type="file" name="image" accept="image/*" class="sr-only" aria-label="Upload thumbnail artikel">
          </label>
        </div>
        @error('image') <p class="mt-2 text-xs text-red-500" role="alert">{{ $message }}</p> @enderror
      </div>

      {{-- Content (rich text) --}}
      <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-2 flex items-center justify-between">
          <label class="text-sm font-bold text-gray-700">
            Isi Artikel <span class="text-red-500" aria-hidden="true">*</span>
          </label>
          <span class="text-xs text-gray-400" id="content-word-count">0 kata</span>
        </div>
        <div id="content-editor" style="height: 420px;"></div>
        {{-- Hidden field actually submitted; kept in sync with the Quill editor by JS below --}}
        <textarea id="content-textarea" name="content" required class="hidden">{{ old('content', $pendingEdit->content ?? $article->content) }}</textarea>
      </div>
    </div>

    {{-- ── Sidebar Column ──────────────────── --}}
    <div class="space-y-5">

      {{-- Publish settings (admin) --}}
      @if($isAdmin)
      <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
          <h2 class="text-sm font-bold text-gray-800">Publikasi</h2>
        </div>
        <div class="space-y-4 p-4">
          <div>
            <label for="status-select" class="mb-1.5 block text-xs font-bold text-gray-600">Status</label>
            <select id="status-select" name="status"
                    class="w-full rounded-lg border-2 border-gray-200 px-3 py-2 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:outline-none focus:ring-0">
              <option value="active"   @selected(old('status',$article->status)==='active')>✅ Active (Publik)</option>
              <option value="draft"    @selected(old('status',$article->status)==='draft')>📝 Draft</option>
              <option value="archived" @selected(old('status',$article->status)==='archived')>📦 Archived</option>
            </select>
          </div>
          <div>
            <label for="created_at" class="mb-1.5 block text-xs font-bold text-gray-600">Tanggal Publikasi</label>
            <input id="created_at" type="date" name="created_at" readonly
                   value="{{ old('created_at', $article->created_at ? \Carbon\Carbon::parse($article->created_at)->format('Y-m-d') : date('Y-m-d')) }}"
                   class="w-full rounded-lg border-2 border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-500 cursor-not-allowed focus:outline-none focus:ring-0">
          </div>
          <input type="hidden" name="revision_note" id="revision_note" value="Pembaruan">
          <div class="pt-2 border-t border-gray-100">
            <button type="submit" class="w-full rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">
              {{ $mode === 'create' ? '+ Publikasikan' : '💾 Simpan Perubahan' }}
            </button>
          </div>
        </div>
      </div>
      @elseif($isLiveEditFlow)
      {{-- Artikel live yang diedit non-admin: satu tombol saja, semantiknya
           "kirim usulan", bukan draft/submit biasa (article status tidak
           berubah sampai admin approve/reject). --}}
      <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
          <h2 class="text-sm font-bold text-gray-800">Usulan Perubahan</h2>
        </div>
        <div class="p-4 space-y-3">
          <div class="rounded-lg bg-blue-50 border border-blue-100 p-3 text-xs text-blue-700">
            Artikel akan tetap tampil seperti sekarang untuk pembaca sampai admin menyetujui perubahan ini.
          </div>
          <button type="submit"
                  class="w-full rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">
            📤 Kirim Usulan ke Admin
          </button>
        </div>
      </div>
      @else
      {{-- Non-admin publish panel (draft/pending/takedown flow) --}}
      <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
          <h2 class="text-sm font-bold text-gray-800">Publikasi</h2>
        </div>
        <div class="p-4 space-y-3">
          <div class="rounded-lg bg-blue-50 border border-blue-100 p-3 text-xs text-blue-700">
            <p class="font-bold mb-1">ℹ️ Alur Publikasi:</p>
            <ul class="space-y-0.5">
              <li>💾 Draft → tersimpan, belum dikirim</li>
              <li>🚀 Submit → dikirim untuk review admin</li>
              <li>✅ Active → disetujui, tampil publik</li>
            </ul>
          </div>
          <button type="submit" name="save_draft" value="1"
                  class="w-full rounded-xl border border-gray-300 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-gray-400">
            💾 Simpan Draft
          </button>
          <button type="submit" name="submit" value="1"
                  class="w-full rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">
            🚀 Submit ke Admin
          </button>
        </div>
      </div>
      @endif

      {{-- SEO Panel (Phase 6) --}}
      <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <button type="button" id="seo-toggle"
                class="flex w-full items-center justify-between border-b border-gray-100 bg-gray-50 px-4 py-3 text-left focus:outline-none"
                aria-expanded="false" aria-controls="seo-panel">
          <span class="text-sm font-bold text-gray-800">🔍 SEO & Meta</span>
          <svg id="seo-chevron" class="h-4 w-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div id="seo-panel" class="hidden p-4 space-y-4">
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="meta_title" class="text-xs font-bold text-gray-600">Meta Title</label>
              <span id="meta_title_count" class="text-xs text-gray-400">0 / 60</span>
            </div>
            <input id="meta_title" type="text" name="meta_title" maxlength="60"
                   value="{{ old('meta_title', $article->meta_title ?? '') }}"
                   placeholder="Judul untuk mesin pencari (opsional)"
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-700 focus:border-brand-600 focus:outline-none focus:ring-0">
          </div>
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="meta_description" class="text-xs font-bold text-gray-600">Meta Description</label>
              <span id="meta_description_count" class="text-xs text-gray-400">0 / 160</span>
            </div>
            <textarea id="meta_description" name="meta_description" rows="3" maxlength="160"
                      placeholder="Deskripsi singkat untuk Google (opsional)"
                      class="w-full resize-none rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-700 focus:border-brand-600 focus:outline-none focus:ring-0">{{ old('meta_description', $article->meta_description ?? '') }}</textarea>
          </div>
          <div>
            <div class="flex items-center justify-between mb-1.5">
              <label for="meta_keywords" class="text-xs font-bold text-gray-600">Meta Keywords</label>
              <span id="meta_keywords_count" class="text-xs text-gray-400">0 / 200</span>
            </div>
            <input id="meta_keywords" type="text" name="meta_keywords" maxlength="200"
                   value="{{ old('meta_keywords', $article->meta_keywords ?? '') }}"
                   placeholder="keyword1, keyword2, keyword3"
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-700 focus:border-brand-600 focus:outline-none focus:ring-0">
          </div>
          <div>
            <label for="canonical_url" class="mb-1.5 block text-xs font-bold text-gray-600">Canonical URL</label>
            <input id="canonical_url" type="url" name="canonical_url"
                   value="{{ old('canonical_url', $article->canonical_url ?? '') }}"
                   placeholder="https://example.com/artikel-asli"
                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-700 focus:border-brand-600 focus:outline-none focus:ring-0">
          </div>
        </div>
      </div>

      {{-- Article stats (edit mode) --}}
      @if($mode === 'edit' && $article->id)
      <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <h2 class="mb-3 text-sm font-bold text-gray-800">Statistik</h2>
        <dl class="space-y-2 text-xs">
          <div class="flex justify-between"><dt class="text-gray-500">Views</dt><dd class="font-semibold">{{ number_format($article->views) }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-500">Komentar</dt><dd class="font-semibold">{{ $article->comments()->where('status','approved')->count() }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-500">Bookmark</dt><dd class="font-semibold">{{ $article->bookmarks()->count() }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-500">Waktu baca</dt><dd class="font-semibold">{{ $article->reading_time }} mnt</dd></div>
        </dl>
      </div>
      @endif
    </div>
  </div>
  </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script>
// Rich text editor (Quill): bold, italic, underline, strike, headings,
// lists, blockquote, link, image — full formatting toolbar for writers.
(function(){
    const hiddenField = document.getElementById('content-textarea');
    const quill = new Quill('#content-editor', {
        theme: 'snow',
        placeholder: 'Tulis konten artikel di sini...',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['blockquote', 'link', 'image'],
                ['clean']
            ]
        }
    });

    // Load existing content (plain text with real newlines from before this
    // editor existed, or HTML from a previous Quill save) into the editor.
    const initial = hiddenField.value || '';
    if (initial.trim().startsWith('<')) {
        quill.root.innerHTML = initial;
    } else if (initial) {
        quill.setText(initial);
    }

    const cnt = document.getElementById('content-word-count');
    const syncAndCount = () => {
        hiddenField.value = quill.root.innerHTML;
        const words = quill.getText().trim().split(/\s+/).filter(Boolean).length;
        if (cnt) cnt.textContent = words + ' kata';
        hiddenField.dispatchEvent(new Event('input', { bubbles: true })); // keep autosave() in app.js in sync
    };
    quill.on('text-change', syncAndCount);
    syncAndCount();

    // Always sync right before the form actually submits, in case the last
    // edit didn't fire a text-change tick yet.
    document.getElementById('article-form').addEventListener('submit', () => {
        hiddenField.value = quill.root.innerHTML;
    });
})();

// SEO panel toggle
(function(){
    const btn = document.getElementById('seo-toggle');
    const panel = document.getElementById('seo-panel');
    const chevron = document.getElementById('seo-chevron');
    if (!btn) return;
    btn.addEventListener('click', () => {
        const open = panel.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', String(!open));
        chevron.classList.toggle('rotate-180');
    });
})();

// Preview "hidup": simpan draft form saat ini (belum disimpan ke DB) ke
// localStorage tepat sebelum tab preview dibuka, supaya article_preview
// bisa menampilkan hasil editan terbaru alih-alih menunggu Simpan/Submit.
// Kalau form disubmit (benar-benar tersimpan), draft lokal ini dihapus
// supaya preview berikutnya balik menampilkan versi tersimpan/live seperti
// biasa, bukan draft basi.
(function(){
    const link = document.getElementById('preview-link');
    const form = document.getElementById('article-form');
    if (!link) return;
    const key = link.dataset.previewKey;

    link.addEventListener('click', () => {
        const title   = document.getElementById('article-title-input')?.value || '';
        const content = document.getElementById('content-textarea')?.value || '';
        try {
            localStorage.setItem(key, JSON.stringify({ title, content, ts: Date.now() }));
        } catch (e) { /* localStorage penuh/diblokir: preview jatuh ke versi tersimpan, tidak fatal */ }
    });

    form?.addEventListener('submit', () => {
        try { localStorage.removeItem(key); } catch (e) {}
    });
})();
</script>

</x-dynamic-component>
