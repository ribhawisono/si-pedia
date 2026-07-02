<x-layouts.app title="Kirim Testimoni — SI-Pedia" footer="min"
               meta_description="Bagikan pengalamanmu menggunakan SI-Pedia.">

<div class="bg-ink-900 py-10">
  <div class="mx-auto max-w-[700px] px-4 sm:px-6 lg:px-8">
    <nav class="mb-3 flex items-center gap-2 text-xs text-white/50" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
      <span aria-hidden="true">›</span>
      <a href="{{ route('review.index') }}" class="hover:text-white transition">Review</a>
      <span aria-hidden="true">›</span>
      <span class="text-white">Kirim Testimoni</span>
    </nav>
    <h1 class="text-2xl font-extrabold text-white">Bagikan Pengalamanmu</h1>
    <p class="mt-1 text-sm text-white/60">Testimonimu akan membantu sesama civitas akademika.</p>
  </div>
</div>

<main class="bg-gray-50 min-h-[60vh] py-8" id="main-content">
  <div class="mx-auto max-w-[700px] px-4 sm:px-6 lg:px-8">

    @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm font-semibold text-green-700" role="alert">
      ✅ {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3" role="alert">
      <ul class="text-sm text-red-600 space-y-0.5">
        @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
      </ul>
    </div>
    @endif

    <form action="{{ route('review.store') }}" method="POST" data-validate class="card p-6 space-y-5">
      @csrf

      <div>
        <label for="review-title" class="form-label">Judul Testimoni <span class="text-red-500">*</span></label>
        <input id="review-title" type="text" name="title" required maxlength="255"
               value="{{ old('title') }}"
               placeholder="Contoh: Platform yang Sangat Informatif!"
               class="form-input">
        @error('title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label for="review-type" class="form-label">Kategori <span class="text-red-500">*</span></label>
        <select id="review-type" name="type" required class="form-input">
          <option value="">-- Pilih kategori --</option>
          @foreach(['Mahasiswa','Alumni','Dosen','Masyarakat Umum','Media','Lainnya'] as $t)
          <option value="{{ $t }}" @selected(old('type') === $t)>{{ $t }}</option>
          @endforeach
        </select>
        @error('type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label for="review-desc" class="form-label">
          Isi Testimoni <span class="text-red-500">*</span>
          <span class="font-normal text-gray-400">(maks. 2000 karakter)</span>
        </label>
        <textarea id="review-desc" name="description" required rows="5" maxlength="2000"
                  data-counter="2000"
                  placeholder="Ceritakan pengalamanmu menggunakan SI-Pedia..."
                  class="form-input resize-y">{{ old('description') }}</textarea>
        @error('description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="rounded-lg bg-blue-50 border border-blue-100 p-3 text-xs text-blue-700">
        ℹ️ Testimonimu akan ditinjau oleh admin sebelum ditampilkan di halaman Review.
      </div>

      <div class="flex gap-3 pt-1">
        <a href="{{ route('review.index') }}" class="btn btn-ghost flex-1 justify-center">Batal</a>
        <button type="submit" class="btn btn-primary flex-1 justify-center">Kirim Testimoni</button>
      </div>
    </form>
  </div>
</main>
</x-layouts.app>
