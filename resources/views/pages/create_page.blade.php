<x-layouts.app title="Buat Halaman — SI-Pedia">
<main class="mx-auto max-w-[1380px] px-10 py-10">
  <h1 class="page-title">Buat Halaman Baru</h1>
  <p class="mt-1 text-xl text-gray-700">Tambahkan halaman baru untuk website</p>
  <div class="mt-7 rounded-3xl border border-gray-200 p-9 shadow-sm">
    <label class="mb-2 block form-label">Nama Halaman</label>
    <div class="rounded-full border border-gray-300 px-6 py-4 text-lg font-bold text-gray-400">Contoh: Sejarah Prodi</div>
    <label class="mb-2 mt-6 block form-label">Judul Halaman</label>
    <div class="rounded-full border border-gray-300 px-6 py-4 text-lg font-bold text-gray-400">Contoh: Sejarah Program Studi Sistem Informasi</div>
    <label class="mb-2 mt-6 block form-label">Banner / Gambar Sampul</label>
    <div class="grid h-72 w-[460px] place-items-center rounded-2xl border-2 border-gray-200 text-center">
      <div><p class="text-sm font-semibold text-gray-700">Upload Foto/Gambar</p><p class="text-xs text-gray-400">atau drag and drop</p><p class="text-xs text-gray-400">Format: JGP, PNG (Maks. 2MB)</p></div>
    </div>
    <label class="mb-2 mt-6 block text-xl font-bold">Konten Halaman</label>
    <div class="rounded-2xl border border-gray-200">
      <div class="flex items-center gap-1 border-b border-gray-200 px-4 py-3">
        <span class="px-2 text-xl text-gray-700"><b>B</b></span><span class="px-2 text-xl text-gray-700"><i>I</i></span><span class="px-2 text-xl text-gray-700"><u>U</u></span><span class="px-2 text-xl text-gray-700">❝</span><span class="mx-2 h-6 w-px bg-gray-200"></span>
        <span class="px-2 text-xl text-gray-700">☰</span><span class="px-2 text-xl text-gray-700">▤</span><span class="px-2 text-xl text-gray-700">≡</span><span class="mx-2 h-6 w-px bg-gray-200"></span>
        <span class="px-2 text-xl text-gray-700">🔗</span><span class="px-2 text-xl text-gray-700">🖼</span><span class="px-2 text-xl text-gray-700">▦</span><span class="mx-2 h-6 w-px bg-gray-200"></span><span class="px-2 text-xl text-gray-700">↶</span><span class="px-2 text-xl text-gray-700">↷</span>
      </div>
      <div class="min-h-[420px] p-6 text-lg text-gray-400">Tulis konten halaman di sini....</div>
      <div class="border-t border-gray-100 px-6 py-3 text-gray-400">0 words</div>
    </div>
    <h3 class="mt-7 text-2xl font-bold">Status</h3>
    <label class="mt-4 flex items-center gap-3"><span class="h-6 w-6 rounded-full border-2 border-gray-300"></span><div><p class="text-xl">Draft</p><p class="text-gray-400">Simpan sebagai draft</p></div></label>
    <label class="mt-4 flex items-center gap-3"><span class="grid h-6 w-6 place-items-center rounded-full border-2 border-blue-700"><span class="h-3 w-3 rounded-full bg-blue-700"></span></span><div><p class="text-xl">Publish</p><p class="text-gray-400">Terbitkan artikel</p></div></label>
    <div class="mt-6 flex justify-end gap-4">
      <button class="rounded-xl border border-gray-300 px-8 py-3 font-bold">Batal</button>
      <button class="rounded-xl bg-brand-600 px-8 py-3 font-bold text-white">Simpan Halaman</button>
    </div>
  </div>
</main>
</x-layouts.app>
