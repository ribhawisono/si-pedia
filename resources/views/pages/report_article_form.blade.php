<x-layouts.app title="Laporkan Artikel — SI-Pedia">
<main class="mx-auto max-w-[600px] px-8 py-12">

  <div class="mb-8">
    <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 transition">
      ← Kembali
    </a>
    <h1 class="mt-4 text-3xl font-extrabold text-gray-900">Laporkan Artikel</h1>
    <p class="mt-2 text-gray-500 text-sm">Laporkan artikel yang melanggar ketentuan penggunaan SI-Pedia.</p>
  </div>

  {{-- Info artikel yang dilaporkan --}}
  <div class="mb-6 flex items-center gap-4 rounded-xl border border-gray-200 bg-gray-50 px-5 py-4">
    @if($article->image)
      <img src="{{ $article->image_url }}" class="h-12 w-12 rounded-lg object-cover flex-shrink-0" alt="{{ $article->title }}">
    @else
      <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-slate-600 to-slate-800 flex items-center justify-center font-bold text-white text-lg flex-shrink-0">
        {{ strtoupper(substr($article->title, 0, 1)) }}
      </div>
    @endif
    <div class="min-w-0">
      <p class="font-bold text-gray-900 truncate">{{ $article->title }}</p>
      <p class="text-sm text-gray-500">Oleh {{ $article->writer }}</p>
    </div>
    <span class="ml-auto rounded-full bg-red-100 dark:bg-red-900/30 px-3 py-1 text-xs font-bold text-red-600 dark:text-red-300 whitespace-nowrap">
      Akan Dilaporkan
    </span>
  </div>

  @if(session('error'))
    <div class="mb-5 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-600 dark:text-red-300 font-semibold">
      ⚠️ {{ session('error') }}
    </div>
  @endif

  <form action="{{ route('articles.report.store', $article) }}" method="POST"
        class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm space-y-6">
    @csrf

    <div>
      <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Pelaporan <span class="text-red-500">*</span></label>
      <select name="reason" required
              class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
        <option value="">-- Pilih Alasan --</option>
        <option value="Spam" @selected(old('reason') === 'Spam')>Spam atau konten berulang</option>
        <option value="Konten tidak pantas" @selected(old('reason') === 'Konten tidak pantas')>Konten tidak pantas / menyinggung</option>
        <option value="Informasi palsu" @selected(old('reason') === 'Informasi palsu')>Menyebarkan informasi palsu / hoaks</option>
        <option value="Plagiarisme" @selected(old('reason') === 'Plagiarisme')>Plagiarisme / pelanggaran hak cipta</option>
        <option value="Ujaran kebencian" @selected(old('reason') === 'Ujaran kebencian')>Ujaran kebencian atau SARA</option>
        <option value="Konten dewasa" @selected(old('reason') === 'Konten dewasa')>Konten dewasa / tidak pantas</option>
        <option value="Lainnya" @selected(old('reason') === 'Lainnya')>Lainnya</option>
      </select>
      @error('reason') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm font-bold text-gray-700 mb-2">
        Deskripsi Tambahan
        <span class="font-normal text-gray-400">(opsional, maks. 1000 karakter)</span>
      </label>
      <textarea name="description" rows="5" maxlength="1000"
                placeholder="Jelaskan lebih detail mengapa kamu melaporkan artikel ini..."
                class="w-full rounded-xl border-2 border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-brand-600 focus:ring-0 resize-none">{{ old('description') }}</textarea>
      @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
    </div>

    <div class="rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 px-4 py-3 text-xs text-yellow-700 dark:text-yellow-400">
      ⚠️ Laporan palsu atau yang tidak berdasar dapat berakibat pada akunmu sendiri.
      Pastikan kamu hanya melaporkan pelanggaran yang nyata.
    </div>

    <div class="flex gap-3 pt-2">
      <a href="javascript:history.back()"
         class="flex-1 rounded-xl border border-gray-300 py-3 text-sm font-bold text-gray-700 text-center hover:bg-gray-50 transition">
        Batal
      </a>
      <button type="submit"
              class="flex-1 rounded-xl bg-red-600 py-3 text-sm font-bold text-white hover:bg-red-700 transition">
        Kirim Laporan
      </button>
    </div>
  </form>

</main>
</x-layouts.app>
