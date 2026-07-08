<x-layouts.admin title="Takedown Artikel — SI-Pedia" section="articles">
<main class="mx-auto max-w-[600px] px-4 sm:px-8 py-10">

  <div class="mb-6">
    <a href="{{ route('admin.articles.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">← Kembali</a>
    <h1 class="mt-3 page-title">Takedown Artikel</h1>
    <p class="page-subtitle">Menarik artikel dari publik, tapi tetap bisa diperbaiki penulisnya.</p>
  </div>

  <div class="mb-6 flex items-center gap-4 rounded-xl border border-gray-200 bg-gray-50 px-5 py-4">
    @if($article->image)
      <img src="{{ $article->image_url }}" class="h-12 w-12 rounded-lg object-cover flex-shrink-0" alt="{{ $article->title }}">
    @else
      <div class="h-12 w-12 rounded-lg bg-gray-300 flex items-center justify-center text-lg flex-shrink-0">📄</div>
    @endif
    <div class="min-w-0">
      <p class="font-bold text-gray-900 truncate">{{ $article->title }}</p>
      <p class="text-sm text-gray-500">Oleh {{ $article->user->name ?? $article->writer }}</p>
    </div>
  </div>

  <div class="mb-5 rounded-lg bg-purple-50 border border-purple-200 px-4 py-3 text-xs text-purple-700">
    ℹ️ Berbeda dari <strong>Hapus</strong>: artikel akan masuk Trash, tapi penulis tetap bisa melihat &amp; mengedit ulang dari “Artikel Saya” berdasarkan catatan yang kamu tulis di bawah.
  </div>

  <form action="{{ route('admin.articles.takedown', $article) }}" method="POST" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
    @csrf
    <div>
      <label class="form-label">Catatan perbaikan untuk penulis <span class="text-red-500">*</span></label>
      <textarea name="rejection_note" rows="5" required maxlength="1000"
                placeholder="Jelaskan alasan takedown dan apa yang perlu diperbaiki..."
                class="form-input resize-none"></textarea>
    </div>
    <div class="flex gap-3 pt-1">
      <a href="{{ route('admin.articles.index') }}" class="flex-1 rounded-xl border border-gray-300 py-2.5 text-center text-sm font-bold text-gray-700 hover:bg-gray-50 transition">Batal</a>
      <button type="submit" class="flex-1 rounded-xl bg-purple-600 py-2.5 text-sm font-bold text-white hover:bg-purple-700 transition">⬇ Takedown</button>
    </div>
  </form>
</main>
</x-layouts.admin>
