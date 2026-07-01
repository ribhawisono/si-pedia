<x-layouts.app title="{{ $article->title }} — SI-Pedia" footer="full"
    meta_description="{{ Str::limit(strip_tags($article->content), 160) }}"
    og_image="{{ $article->image ? $article->image_url : null }}">
    <main class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($article->image)
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-[400px] object-cover" loading="lazy">
            @else
                <div class="w-full h-[400px] bg-indigo-50 flex items-center justify-center">
                    <span class="text-indigo-200 text-6xl font-bold">No Image</span>
                </div>
            @endif

            <div class="p-8 sm:p-12">
                <div class="flex items-center gap-4 mb-6 text-sm text-gray-500">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 font-medium">
                        {{ $article->category->name ?? 'Uncategorized' }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ $article->created_at->format('M d, Y') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        {{ $article->writer }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-bold text-[#0a0b2f] mb-8 leading-tight">
                    {{ $article->title }}
                </h1>

                <div class="prose prose-lg max-w-none text-gray-600">
                    {!! nl2br(e($article->content)) !!}
                </div>
            </div>
        </article>

        {{-- Comments Section --}}
        <section class="mt-10 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-[#0a0b2f] mb-6">
                Komentar
                @if($article->comments->count())
                    <span class="text-base font-normal text-gray-400">({{ $article->comments->count() }})</span>
                @endif
            </h2>

            @if($article->comments->count())
                <div class="space-y-4 mb-8">
                    @foreach($article->comments as $comment)
                        <div class="flex gap-4 p-4 bg-gray-50 rounded-xl">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[#336cbc] text-white flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($comment->user->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-sm text-[#0a0b2f]">{{ $comment->user->name ?? 'Anonymous' }}</span>
                                    <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600 text-sm">{{ $comment->content }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 mb-8">Belum ada komentar. Jadilah yang pertama!</p>
            @endif

            @auth
                <form method="POST" action="{{ route('comments.store', $article->id) }}">
                    @csrf
                    <div class="mb-3">
                        <textarea name="content" rows="3" required maxlength="1000"
                            placeholder="Tulis komentar Anda..."
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#336cbc] focus:border-transparent outline-none transition resize-none">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#336cbc] text-white font-semibold rounded-xl hover:bg-blue-700 transition text-sm">
                        Kirim Komentar
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="text-[#336cbc] hover:underline font-medium">Login</a> untuk menulis komentar.
                </p>
            @endauth
        </section>
    </main>
</x-layouts.app>
