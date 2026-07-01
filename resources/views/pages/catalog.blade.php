<x-layouts.app title="Katalog Artikel - SI-Pedia" footer="full">
    <main class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-[#0a0b2f] mb-4">Katalog Artikel</h1>
            <p class="text-lg text-gray-600">Eksplorasi kumpulan artikel dan pengetahuan dari kontributor kami.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($articles as $article)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                @if($article->image)
                    <img src="{{ Storage::url($article->image) }}" alt="{{ $article->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-indigo-50 flex items-center justify-center">
                        <span class="text-indigo-200 text-2xl font-bold">No Image</span>
                    </div>
                @endif
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-indigo-50 text-[#336cbc]">
                            {{ $article->category->name ?? 'Uncategorized' }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $article->created_at->format('M d, Y') }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-[#0a0b2f] mb-2 line-clamp-2">
                        <a href="{{ route('articles.show', $article->slug ?? $article->id) }}" class="hover:text-[#336cbc]">
                            {{ $article->title }}
                        </a>
                    </h2>
                    <p class="text-gray-600 mb-4 line-clamp-3 text-sm flex-1">
                        {{ Str::limit(strip_tags($article->content), 120) }}
                    </p>
                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-sm text-gray-500 font-medium">{{ $article->writer }}</span>
                        <a href="{{ route('articles.show', $article->slug ?? $article->id) }}" class="text-sm font-bold text-[#336cbc] hover:text-[#0a0b2f]">
                            Read More &rarr;
                        </a>
                    </div>
                </div>
            </article>
            @empty
            <div class="col-span-full py-12 text-center text-gray-500 bg-gray-50 rounded-2xl border border-gray-200">
                Belum ada artikel yang dipublikasikan.
            </div>
            @endforelse
        </div>

        <div class="mt-12">
            {{ $articles->links() }}
        </div>
    </main>
</x-layouts.app>
