<x-layouts.admin title="Edit Homepage — SI-Pedia" section="dashboard">
<div class="mx-auto max-w-[900px]">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">Edit Homepage</h1>
        <p class="mt-2 text-sm text-gray-500">Ubah konten yang tampil di halaman utama website.</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-5 py-3 text-sm text-green-700 font-medium">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 px-5 py-3 text-sm text-red-700">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('admin.homepage.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Hero Title --}}
        <div class="rounded-xl border border-gray-200 p-6 shadow-sm">
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Judul Hero</label>
            <input type="text" name="title" value="{{ old('title', $page->title ?? 'Sistem Informasi Pedia') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-lg font-semibold text-gray-900 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 outline-none transition">
            <p class="mt-2 text-xs text-gray-400">Judul besar yang tampil di bagian atas homepage.</p>
        </div>

        {{-- Hero Description --}}
        <div class="rounded-xl border border-gray-200 p-6 shadow-sm">
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Deskripsi Hero</label>
            <textarea name="content" rows="4"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-700 focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 outline-none transition resize-none">{{ old('content', $page->content ?? 'Platform digital untuk menjelajahi artikel, informasi dosen, dan sumber daya akademik Program Studi Sistem Informasi.') }}</textarea>
            <p class="mt-2 text-xs text-gray-400">Deskripsi singkat di bawah judul hero.</p>
        </div>

        {{-- Banner --}}
        <div class="rounded-xl border border-gray-200 p-6 shadow-sm">
            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Banner Image (opsional)</label>
            @if(!empty($page->banner))
                <div class="mb-3">
                    <img src="{{ Storage::url($page->banner) }}" class="h-32 rounded-lg object-cover border">
                </div>
            @endif
            <input type="file" name="banner" accept="image/*"
                class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-brand-600/10 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-600 dark:file:text-brand-400 hover:file:bg-brand-600/20 transition">
            <p class="mt-2 text-xs text-gray-400">Maksimal 10MB. Format: JPG, PNG, WEBP.</p>
        </div>

        {{-- Submit --}}
        <div class="flex gap-4 pt-2">
            <a href="{{ route('admin.panel') }}" class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" class="rounded-lg bg-brand-600 px-8 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>

</div>
</x-layouts.admin>
