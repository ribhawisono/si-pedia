<x-layouts.app :title="'Preview: ' . $article->title" footer="none">
{{-- Bukan `fixed` lagi: sebelumnya banner ini fixed top-0 z-50 sehingga
     menimpa/menutupi navbar (<x-navbar> di layout ada di atas juga).
     Sekarang mengalir normal, otomatis muncul di bawah navbar. --}}
<div class="flex flex-wrap items-center justify-between gap-2 bg-yellow-400 px-4 sm:px-6 py-2 text-xs sm:text-sm font-bold text-yellow-900 shadow" role="alert">
  <span>👁 MODE PREVIEW — belum dipublikasikan</span>
  <a href="javascript:history.back()" class="rounded-lg border border-yellow-700 px-3 sm:px-4 py-1 hover:bg-yellow-500 transition whitespace-nowrap">← Kembali</a>
</div>
<div>
  <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6">

    {{-- Admin review actions: only shown while this article is actually
         awaiting approval, so approve/reject is decided AFTER reading the
         full content here — not blind from the short excerpt on the
         pending list page. --}}
    @if($article->status === 'pending' && auth()->user()->role === 'admin')
    <div class="mb-6 rounded-2xl border border-yellow-200 bg-yellow-50 p-4 sm:p-5">
      <p class="mb-3 text-sm font-bold text-yellow-800">Artikel ini menunggu persetujuan — tinjau isinya di bawah, lalu putuskan:</p>
      <div class="flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.articles.approve', $article) }}" method="POST" class="sm:w-40">
          @csrf @method('PATCH')
          <button type="submit" class="w-full rounded-xl bg-green-500 py-2.5 text-sm font-bold text-white hover:bg-green-600 transition">
            ✅ Approve
          </button>
        </form>
        <form action="{{ route('admin.articles.reject', $article) }}" method="POST" class="flex-1 flex flex-col sm:flex-row gap-2">
          @csrf @method('PATCH')
          <textarea name="rejection_note" rows="1" required maxlength="1000"
                    placeholder="Catatan perbaikan untuk penulis (wajib diisi jika menolak)..."
                    class="flex-1 rounded-xl border border-yellow-300 px-3 py-2 text-sm text-gray-700 resize-none focus:border-red-400 focus:ring-0"></textarea>
          <button type="submit" class="rounded-xl bg-red-100 px-5 py-2.5 text-sm font-bold text-red-600 hover:bg-red-200 transition whitespace-nowrap">
            ❌ Tolak &amp; Kirim Catatan
          </button>
        </form>
      </div>
    </div>
    @endif

    <article class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
      @if($article->image_url)
      <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-64 sm:h-80 object-cover">
      @endif
      <div class="p-6 sm:p-10">
        <div class="flex flex-wrap gap-3 mb-4 text-sm">
          <span class="rounded-full bg-brand-600/10 px-3 py-1 font-semibold text-brand-700">{{ $article->category->name ?? 'Umum' }}</span>
          <span class="text-gray-400">{{ $article->reading_time }} mnt baca</span>
          @if($article->tags->isNotEmpty())
            @foreach($article->tags as $tag)
            <span class="rounded-full border border-gray-200 px-3 py-0.5 text-xs font-semibold text-gray-600">#{{ $tag->name }}</span>
            @endforeach
          @endif
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">{{ $article->title }}</h1>
        <p class="mb-6 text-sm text-gray-500">Oleh {{ $article->user->name ?? $article->writer }}</p>
        {{-- Content is sanitized HTML from the Quill editor (see ArticleService::sanitizeHtml) --}}
        <div class="prose prose-lg max-w-none text-gray-700 text-justify">{!! $article->content !!}</div>

        @if($article->meta_title || $article->meta_description)
        <div class="mt-8 rounded-xl bg-gray-50 border border-gray-200 p-4 text-xs space-y-2">
          <p class="font-bold text-gray-500 uppercase tracking-wide">SEO Preview</p>
          @if($article->meta_title)
          <div><span class="text-gray-400">Title:</span> <span class="font-semibold text-blue-600">{{ $article->meta_title }}</span></div>
          @endif
          @if($article->meta_description)
          <div><span class="text-gray-400">Description:</span> <span class="text-gray-700">{{ $article->meta_description }}</span></div>
          @endif
          @if($article->meta_keywords)
          <div><span class="text-gray-400">Keywords:</span> <span class="text-gray-700">{{ $article->meta_keywords }}</span></div>
          @endif
        </div>
        @endif
      </div>
    </article>
  </div>
</div>
</x-layouts.app>
