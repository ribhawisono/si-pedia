<x-layouts.app title="Buat Halaman — SI-Pedia">
<main class="mx-auto max-w-[1000px] px-4 sm:px-8 lg:px-10 py-6 sm:py-10">
  <h1 class="page-title">Buat Halaman Baru</h1>
  <p class="mt-1 text-sm sm:text-base text-gray-700">Tambahkan halaman baru untuk website.</p>

  @if(session('success'))
  <div class="mt-5 rounded-xl bg-green-50 border border-green-200 px-5 py-3 text-sm font-semibold text-green-700">
    ✅ {{ session('success') }}
  </div>
  @endif

  @if($errors->any())
  <div class="mt-5 rounded-xl bg-red-50 border border-red-200 px-5 py-3 text-sm text-red-700">
    <ul class="list-disc pl-5">
      @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
    </ul>
  </div>
  @endif

  <form action="{{ route('admin.pages.store') }}" method="POST" data-validate
        class="mt-6 sm:mt-7 rounded-2xl sm:rounded-3xl border border-gray-200 p-5 sm:p-9 shadow-sm space-y-6">
    @csrf

    <div>
      <label for="page-name" class="mb-2 block form-label">Nama Halaman (slug) <span class="text-red-500">*</span></label>
      <input id="page-name" type="text" name="name" required maxlength="255" value="{{ old('name') }}"
             placeholder="Contoh: sejarah-prodi"
             class="w-full rounded-xl border border-gray-300 px-4 sm:px-6 py-3 text-sm sm:text-base font-semibold text-gray-800 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 outline-none transition">
      <p class="mt-1 text-xs text-gray-400">Identifier unik untuk halaman (huruf kecil, tanpa spasi).</p>
      @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    <div>
      <label for="page-title" class="mb-2 block form-label">Judul Halaman <span class="text-red-500">*</span></label>
      <input id="page-title" type="text" name="title" required maxlength="255" value="{{ old('title') }}"
             placeholder="Contoh: Sejarah Program Studi Sistem Informasi"
             class="w-full rounded-xl border border-gray-300 px-4 sm:px-6 py-3 text-sm sm:text-base font-semibold text-gray-800 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 outline-none transition">
      @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    <div>
      <label for="page-content" class="mb-2 block form-label">Konten Halaman <span class="text-red-500">*</span></label>
      <textarea id="page-content" name="content" required rows="10"
                placeholder="Tulis konten halaman di sini..."
                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm text-gray-800 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 outline-none transition resize-y">{{ old('content') }}</textarea>
      @error('content')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="mb-3 block text-base sm:text-lg font-bold text-gray-900">Status</label>
      <div class="space-y-2">
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="radio" name="status" value="draft" @checked(old('status', 'draft') === 'draft') class="h-5 w-5 text-brand-600 focus:ring-brand-600">
          <div><p class="text-sm font-semibold text-gray-800">Draft</p><p class="text-xs text-gray-400">Simpan sebagai draft, belum tampil publik</p></div>
        </label>
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="radio" name="status" value="active" @checked(old('status') === 'active') class="h-5 w-5 text-brand-600 focus:ring-brand-600">
          <div><p class="text-sm font-semibold text-gray-800">Publish</p><p class="text-xs text-gray-400">Terbitkan halaman ke publik</p></div>
        </label>
      </div>
      @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-col sm:flex-row sm:justify-end gap-3 pt-2">
      <a href="{{ route('admin.panel') }}" class="rounded-xl border border-gray-300 px-8 py-3 text-center font-bold text-gray-700 hover:bg-gray-50 transition">Batal</a>
      <button type="submit" class="rounded-xl bg-brand-600 px-8 py-3 font-bold text-white hover:bg-brand-700 transition">Simpan Halaman</button>
    </div>
  </form>
</main>
</x-layouts.app>
