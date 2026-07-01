@php
  $isAdmin   = $isAdmin ?? (auth()->user()->role === 'admin');
  $backRoute  = $isAdmin ? route('admin.articles.index') : route('articles.my');
  $storeRoute = $isAdmin
    ? ($mode === 'create' ? route('admin.articles.store') : route('admin.articles.update', $article))
    : ($mode === 'create' ? route('articles.store')       : route('articles.update', $article));
@endphp
<x-layouts.app :title="$mode === 'create' ? 'Tulis Artikel — SI-Pedia' : 'Edit Artikel — SI-Pedia'">
<main class="mx-auto max-w-[1440px] px-8 py-7">
  <div class="flex items-start justify-between">
    <div>
      <h1 class="text-5xl font-black tracking-tight">{{ $mode === 'create' ? 'Tulis Artikel' : 'Edit Artikel' }}</h1>
      <p class="mt-1 text-gray-700">
        @if($isAdmin)
          {{ $mode === 'create' ? 'Tambah artikel baru ke sistem.' : 'Update informasi artikel.' }}
        @else
          Tulis artikel dan submit ke admin untuk dipublikasikan.
        @endif
      </p>
    </div>
    <a href="{{ $backRoute }}" class="rounded-lg bg-gray-100 px-5 py-2.5 text-sm font-bold text-gray-700 shadow hover:bg-gray-200 transition">← Kembali</a>
  </div>

  @if($errors->any())
    <div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-5 py-3">
      <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data">
  @csrf
  @if($mode === 'edit') @method('PUT') @endif

  <div class="mt-6 grid grid-cols-[1.8fr_1fr] gap-6">

    {{-- KONTEN UTAMA --}}
    <div class="space-y-5 rounded-2xl border border-gray-200 p-6 shadow-sm bg-white">

      <div>
        <label class="mb-1 block text-lg font-bold">Judul Artikel</label>
        <input type="text" name="title" value="{{ old('title', $article->title) }}" required
               class="w-full rounded-xl border-2 border-gray-200 px-5 py-3 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
        @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
      </div>

      <div class="grid grid-cols-2 gap-5">
        <div>
          <label class="mb-1 block text-lg font-bold">Kategori</label>
          <select name="category_id" required class="w-full rounded-xl border-2 border-gray-200 px-5 py-3 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" @selected(old('category_id', $article->category_id) == $cat->id)>{{ $cat->name }}</option>
            @endforeach
          </select>
          @error('category_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        {{-- Admin: bisa ubah nama penulis. Non-admin: nama diisi otomatis --}}
        @if($isAdmin)
        <div>
          <label class="mb-1 block text-lg font-bold">Penulis</label>
          <input type="text" name="writer" value="{{ old('writer', $article->writer ?? auth()->user()->name) }}" required
                 class="w-full rounded-xl border-2 border-gray-200 px-5 py-3 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
          @error('writer') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>
        @else
        <div>
          <label class="mb-1 block text-lg font-bold">Penulis</label>
          <div class="w-full rounded-xl border-2 border-gray-100 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-500">
            {{ auth()->user()->name }}
          </div>
        </div>
        @endif
      </div>

      {{-- Admin saja yang bisa atur tanggal & status --}}
      @if($isAdmin)
      <div class="grid grid-cols-2 gap-5">
        <div>
          <label class="mb-1 block text-lg font-bold">Tanggal Dibuat</label>
          <input type="date" name="created_at"
                 value="{{ old('created_at', $article->created_at ? \Carbon\Carbon::parse($article->created_at)->format('Y-m-d') : date('Y-m-d')) }}"
                 required class="w-full rounded-xl border-2 border-gray-200 px-5 py-3 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
        </div>
        <div>
          <label class="mb-1 block text-lg font-bold">Status</label>
          <select name="status" class="w-full rounded-xl border-2 border-gray-200 px-5 py-3 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
            <option value="active"  @selected(old('status', $article->status) === 'active')>Active</option>
            <option value="draft"   @selected(old('status', $article->status) === 'draft')>Draft</option>
            <option value="pending" @selected(old('status', $article->status) === 'pending')>Pending</option>
          </select>
        </div>
      </div>
      @endif

      <div>
        <label class="mb-1 block text-lg font-bold">Thumbnail</label>
        <div class="flex gap-4">
          @if($article->image)
            <img src="{{ $article->image_url }}" class="h-[120px] w-[110px] rounded-lg object-cover">
          @else
            <div class="h-[120px] w-[110px] rounded-lg bg-gray-200 flex items-center justify-center text-xs text-gray-500">No Img</div>
          @endif
          <label class="cursor-pointer grid h-[120px] w-[150px] place-items-center rounded-lg border-2 border-gray-200 text-center text-[10px] text-gray-500 hover:bg-gray-50">
            ⊕<br>Klik untuk upload<br>JPG, PNG, WEBP<br>Max 10 MB
            <input type="file" name="image" accept="image/*" class="hidden">
          </label>
        </div>
        @error('image') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
      </div>

      <div>
        <label class="mb-1 block text-sm font-bold text-gray-700" for="tags-input">
          Tags
          <span class="font-normal text-gray-400">(pisahkan dengan koma, contoh: AI, Machine Learning)</span>
        </label>
        <input id="tags-input" type="text" name="tags"
               value="{{ old('tags', $article->tags->pluck('name')->implode(', ') ?? '') }}"
               placeholder="Contoh: Akademik, AI, Machine Learning"
               class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-brand-600 focus:ring-0 transition">
        <p class="mt-1 text-xs text-gray-400">Tag membantu pembaca menemukan artikel serupa.</p>
      </div>

      <div>
        <label class="mb-1 block text-lg font-bold">Isi Artikel</label>
        <textarea name="content" rows="12" required
                  class="w-full rounded-xl border-2 border-gray-200 p-4 text-sm font-semibold leading-relaxed text-gray-800 focus:border-brand-600 focus:ring-0">{{ old('content', $article->content) }}</textarea>
        @error('content') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
      </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-5">
      <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="bg-tablehead px-4 py-3 text-lg font-bold">Publikasi</div>
        <div class="space-y-3 p-4 text-sm">
          <div class="flex justify-between">
            <span class="font-bold text-gray-500">Status</span>
            <span class="font-bold text-gray-900">{{ ucfirst($article->status ?? 'Draft') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="font-bold text-gray-500">Visibilitas</span>
            <span class="font-bold text-gray-900">{{ $isAdmin ? 'Public' : 'Perlu Approval Admin' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="font-bold text-gray-500">Views</span>
            <span class="font-bold text-gray-900">{{ $article->views ?? 0 }}</span>
          </div>
        </div>

        @if($isAdmin)
        <div class="bg-gray-50 p-4 text-right">
          <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-bold text-white shadow hover:bg-indigo-700">
            Simpan Perubahan
          </button>
        </div>
        @else
        {{-- Non-admin: dua tombol — Simpan Draft atau Submit --}}
        <div class="bg-gray-50 p-4 space-y-2">
          <p class="text-xs text-gray-500 mb-3">
            📌 Artikel yang di-submit akan masuk ke antrian review admin sebelum dipublikasikan.
          </p>
          <button type="submit" name="save_draft" value="1"
                  class="w-full rounded-md bg-gray-200 px-5 py-2 text-sm font-bold text-gray-700 hover:bg-gray-300 transition">
            💾 Simpan sebagai Draft
          </button>
          <button type="submit" name="submit" value="1"
                  class="w-full rounded-md bg-brand-600 px-5 py-2 text-sm font-bold text-white shadow hover:bg-brand-700 transition">
            🚀 Submit ke Admin
          </button>
        </div>
        @endif
      </div>

      @if(!$isAdmin)
      <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-700">
        <p class="font-bold mb-1">ℹ️ Cara kerja artikel:</p>
        <ul class="space-y-1 list-disc list-inside text-xs">
          <li><strong>Draft</strong> — tersimpan, hanya kamu yang bisa lihat</li>
          <li><strong>Submit</strong> — dikirim ke admin untuk direview</li>
          <li><strong>Active</strong> — disetujui admin, tampil publik</li>
        </ul>
      </div>
      @endif
    </div>

  </div>
  </form>
</main>
</x-layouts.app>
