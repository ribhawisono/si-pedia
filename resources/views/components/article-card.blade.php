@props(['article', 'variant' => 'list'])

@php $url = route('articles.show', $article->slug); @endphp

@if($variant === 'grid')
<a href="{{ $url }}" class="group block overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-brand-600">
    <div class="relative h-48 overflow-hidden bg-gray-100">
        @if($article->image_url)
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200" aria-hidden="true">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/></svg>
            </div>
        @endif
        <div class="absolute top-3 left-3">
            <span class="rounded-md bg-brand-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm">
                {{ $article->category->name ?? 'Umum' }}
            </span>
        </div>
    </div>
    <div class="p-5">
        <h3 class="text-lg font-bold leading-snug text-gray-900 dark:text-white line-clamp-2 group-hover:text-brand-700 transition-colors">
            {{ $article->title }}
        </h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ Str::limit(strip_tags($article->content), 100) }}</p>
        <div class="mt-3 flex items-center justify-between text-xs text-gray-400">
            <span>{{ $article->created_at->translatedFormat('j M Y') }}</span>
            <span>{{ $article->reading_time }} mnt</span>
        </div>
    </div>
</a>

@elseif($variant === 'compact')
<a href="{{ $url }}" class="group flex items-start gap-3 rounded-xl p-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition focus:outline-none focus:ring-2 focus:ring-brand-600">
    @if($article->image_url)
        <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="h-16 w-16 flex-shrink-0 rounded-lg object-cover" loading="lazy">
    @else
        <div class="h-16 w-16 flex-shrink-0 rounded-lg bg-gray-100" aria-hidden="true"></div>
    @endif
    <div class="min-w-0">
        <p class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 group-hover:text-brand-700 transition-colors">{{ $article->title }}</p>
        <p class="mt-0.5 text-xs text-gray-400">{{ $article->reading_time }} mnt · {{ number_format($article->views) }}×</p>
    </div>
</a>

@else {{-- list (default) --}}
<article class="flex gap-4 rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 shadow-sm hover:shadow-md transition-all group">
    <div class="flex-shrink-0">
        @if($article->image_url)
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="h-24 w-32 rounded-xl object-cover sm:h-28 sm:w-36" loading="lazy">
        @else
            <div class="flex h-24 w-32 items-center justify-center rounded-xl bg-gradient-to-br from-brand-50 to-brand-100 sm:h-28 sm:w-36" aria-hidden="true">
                <svg class="h-8 w-8 text-brand-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5"/></svg>
            </div>
        @endif
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-2">
            <span class="rounded-md bg-brand-600/10 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
                {{ $article->category->name ?? 'Umum' }}
            </span>
            <time class="text-xs text-gray-400" datetime="{{ $article->created_at->toISOString() }}">{{ $article->created_at->translatedFormat('j M Y') }}</time>
            <span class="text-xs text-gray-400">{{ $article->reading_time }} mnt</span>
        </div>
        <h2 class="font-bold text-gray-900 dark:text-white leading-snug line-clamp-2 group-hover:text-brand-700 transition-colors">
            <a href="{{ $url }}" class="focus:outline-none focus:underline">{{ $article->title }}</a>
        </h2>
        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ Str::limit(strip_tags($article->content), 130) }}</p>
    </div>
</article>
@endif
