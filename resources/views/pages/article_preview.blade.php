<x-layouts.app :title="'Preview: ' . $article->title" footer="none">
<div class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between bg-yellow-400 px-6 py-2 text-sm font-bold text-yellow-900 shadow" role="alert">
  <span>👁 MODE PREVIEW — Artikel ini belum dipublikasikan</span>
  <a href="javascript:history.back()" class="rounded-lg border border-yellow-700 px-4 py-1 hover:bg-yellow-500 transition">← Kembali Edit</a>
</div>
<div class="pt-12">
  <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6">
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
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-6">{{ $article->title }}</h1>
        <div class="prose prose-lg max-w-none text-gray-700">{!! nl2br(e($article->content)) !!}</div>

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
