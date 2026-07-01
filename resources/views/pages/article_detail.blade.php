<x-layouts.app :title="$article->title . ' — SI-Pedia'" footer="full"
               :meta_description="Str::limit(strip_tags($article->content), 160)"
               :og_image="$article->image_url">

{{-- Reading Progress Bar --}}
<div id="reading-progress-bar" class="fixed top-[68px] left-0 z-40 h-0.5 bg-brand-600 transition-all duration-100 will-change-transform" style="width:0%" aria-hidden="true" role="presentation"></div>

<main class="bg-gray-50" id="main-content">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">

      {{-- ─── Main article column ──────────────────────────────────────────── --}}
      <article class="flex-1 min-w-0" aria-labelledby="article-title">

        {{-- Back breadcrumb --}}
        <nav class="mb-5 flex items-center gap-2 text-sm text-gray-500" aria-label="Breadcrumb">
          <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
          <svg class="h-3.5 w-3.5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          <a href="{{ route('catalog') }}" class="hover:text-brand-600 transition">Katalog</a>
          <svg class="h-3.5 w-3.5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
          <span class="truncate text-gray-700 font-medium max-w-[200px]">{{ $article->title }}</span>
        </nav>

        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
          {{-- Hero Image --}}
          @if($article->image_url)
          <div class="relative h-64 sm:h-80 lg:h-[420px] overflow-hidden">
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                 class="h-full w-full object-cover" loading="eager">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
          </div>
          @endif

          <div class="p-6 sm:p-8 lg:p-10" id="article-content">
            {{-- Meta row --}}
            <div class="flex flex-wrap items-center gap-3 mb-5 text-sm">
              <a href="{{ route('catalog', ['category' => $article->category_id]) }}"
                 class="rounded-full bg-brand-600/10 px-3 py-1 font-semibold text-brand-700 hover:bg-brand-600/20 transition">
                {{ $article->category->name ?? 'Umum' }}
              </a>
              <time datetime="{{ $article->created_at->toISOString() }}" class="text-gray-500">
                {{ $article->created_at->translatedFormat('j F Y') }}
              </time>
              <span class="flex items-center gap-1 text-gray-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $article->reading_time }} menit baca
              </span>
              <span class="flex items-center gap-1 text-gray-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ number_format($article->views) }} dibaca
              </span>
            </div>

            {{-- Title --}}
            <h1 id="article-title" class="text-2xl font-extrabold leading-tight text-gray-900 sm:text-3xl lg:text-4xl mb-4">
              {{ $article->title }}
            </h1>

            {{-- Tags --}}
            @if($article->tags->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-6" aria-label="Tag artikel">
              @foreach($article->tags as $tag)
              <a href="{{ route('tags.show', $tag->slug) }}"
                 class="rounded-full border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-600 hover:border-brand-300 hover:text-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-600">
                #{{ $tag->name }}
              </a>
              @endforeach
            </div>
            @endif

            {{-- Content --}}
            <div class="prose prose-gray prose-lg max-w-none text-gray-700 leading-relaxed">
              {!! nl2br(e($article->content)) !!}
            </div>

            {{-- Tags bottom (repeat for discoverability) --}}
            @if($article->tags->isNotEmpty())
            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-wrap gap-2">
              <span class="text-xs font-bold text-gray-400 mr-1">Tags:</span>
              @foreach($article->tags as $tag)
              <a href="{{ route('tags.show', $tag->slug) }}"
                 class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 hover:bg-brand-100 hover:text-brand-700 transition">
                #{{ $tag->name }}
              </a>
              @endforeach
            </div>
            @endif

            {{-- Social Share + Bookmark --}}
            <div class="mt-6 pt-6 border-t border-gray-100">
              <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-bold text-gray-500">Bagikan:</span>

                {{-- Twitter/X --}}
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}"
                   target="_blank" rel="noopener noreferrer"
                   aria-label="Bagikan ke Twitter"
                   class="flex h-9 w-9 items-center justify-center rounded-full bg-black text-white hover:bg-gray-800 transition focus:outline-none focus:ring-2 focus:ring-black">
                  <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>

                {{-- WhatsApp --}}
                <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}"
                   target="_blank" rel="noopener noreferrer"
                   aria-label="Bagikan ke WhatsApp"
                   class="flex h-9 w-9 items-center justify-center rounded-full bg-green-500 text-white hover:bg-green-600 transition focus:outline-none focus:ring-2 focus:ring-green-500">
                  <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </a>

                {{-- Facebook --}}
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                   target="_blank" rel="noopener noreferrer"
                   aria-label="Bagikan ke Facebook"
                   class="flex h-9 w-9 items-center justify-center rounded-full bg-[#1877F2] text-white hover:bg-[#166FE5] transition focus:outline-none focus:ring-2 focus:ring-[#1877F2]">
                  <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>

                {{-- Copy link --}}
                <button id="copy-link-btn"
                        data-url="{{ url()->current() }}"
                        aria-label="Salin tautan artikel"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200 text-gray-700 hover:bg-gray-300 transition focus:outline-none focus:ring-2 focus:ring-gray-400">
                  <svg id="copy-icon" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                  <svg id="check-icon" class="hidden h-4 w-4 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                </button>

                {{-- Bookmark --}}
                @auth
                <button id="bookmark-btn"
                        data-article="{{ $article->id }}"
                        data-bookmarked="{{ $isBookmarked ? 'true' : 'false' }}"
                        data-url="{{ route('bookmarks.toggle', $article) }}"
                        aria-label="{{ $isBookmarked ? 'Hapus dari bookmark' : 'Tambah ke bookmark' }}"
                        aria-pressed="{{ $isBookmarked ? 'true' : 'false' }}"
                        class="ml-auto flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-brand-600
                               {{ $isBookmarked ? 'border-brand-600 bg-brand-600/10 text-brand-700' : 'border-gray-200 text-gray-600 hover:border-brand-300 hover:text-brand-700' }}">
                  <svg id="bookmark-icon" class="h-4 w-4 {{ $isBookmarked ? 'fill-brand-600' : '' }}"
                       fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                  </svg>
                  <span id="bookmark-label">{{ $isBookmarked ? 'Tersimpan' : 'Simpan' }}</span>
                </button>
                @endauth
              </div>
            </div>
          </div>
        </div>

        {{-- Author Card --}}
        @if($article->user)
        <div class="mt-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm flex items-start gap-4" aria-label="Informasi penulis">
          <img src="{{ $article->user->avatar_url }}" alt="Foto {{ $article->user->name }}"
               class="h-14 w-14 rounded-full object-cover flex-shrink-0" loading="lazy">
          <div>
            <p class="text-xs font-bold uppercase tracking-wide text-gray-400 mb-1">Ditulis oleh</p>
            <p class="font-extrabold text-gray-900">{{ $article->user->name }}</p>
            <span class="inline-block rounded-full bg-brand-600/10 px-2.5 py-0.5 text-xs font-semibold text-brand-700 mt-1">
              {{ ucfirst($article->user->role) }}
            </span>
            <p class="mt-2 text-sm text-gray-500">Kontributor SI-Pedia — Program Studi Sistem Informasi Universitas Indraprasta PGRI.</p>
          </div>
        </div>
        @endif

        {{-- Comments --}}
        <section class="mt-6 rounded-2xl border border-gray-100 bg-white shadow-sm" aria-labelledby="comments-heading">
          <div class="border-b border-gray-100 p-6">
            <h2 id="comments-heading" class="text-xl font-extrabold text-gray-900 flex items-center gap-2">
              Komentar
              <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-sm font-bold text-gray-600"
                    aria-label="{{ $article->comments->count() }} komentar">
                {{ $article->comments->count() }}
              </span>
            </h2>
          </div>

          <div class="p-6">
            @if($article->comments->isEmpty())
            <div class="py-8 text-center" role="status">
              <p class="text-gray-400 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
            </div>
            @else
            <div class="space-y-4 mb-6" role="list" aria-label="Daftar komentar">
              @foreach($article->comments as $comment)
              <div class="flex gap-3" role="listitem">
                <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name ?? 'A') . '&background=336cbc&color=fff&size=80' }}"
                     alt="Foto {{ $comment->user->name ?? 'Anonim' }}"
                     class="h-9 w-9 flex-shrink-0 rounded-full object-cover" loading="lazy">
                <div class="flex-1 rounded-xl bg-gray-50 p-4">
                  <div class="mb-1 flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900">{{ $comment->user->name ?? 'Anonim' }}</span>
                    <time datetime="{{ $comment->created_at->toISOString() }}" class="text-xs text-gray-400">
                      {{ $comment->created_at->diffForHumans() }}
                    </time>
                  </div>
                  <p class="text-sm text-gray-700">{{ $comment->content }}</p>
                </div>
              </div>
              @endforeach
            </div>
            @endif

            {{-- Comment form --}}
            @auth
            <form method="POST" action="{{ route('comments.store', $article->id) }}" aria-label="Form komentar">
              @csrf
              @if(session('success') && str_contains(session('success'), 'Komentar'))
              <div class="mb-3 rounded-xl bg-green-50 border border-green-200 px-4 py-2.5 text-sm font-semibold text-green-700" role="alert">
                ✅ {{ session('success') }}
              </div>
              @endif
              <div class="flex gap-3">
                <img src="{{ auth()->user()->avatar_url }}" alt="Foto kamu" class="h-9 w-9 flex-shrink-0 rounded-full object-cover" loading="lazy">
                <div class="flex-1">
                  <label for="comment-input" class="sr-only">Tulis komentar kamu</label>
                  <textarea id="comment-input" name="content" rows="3" required maxlength="1000"
                            placeholder="Tulis komentar kamu..."
                            class="w-full resize-none rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-800 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600 transition">{{ old('content') }}</textarea>
                  @error('content')
                  <p class="mt-1 text-xs text-red-500" role="alert">{{ $message }}</p>
                  @enderror
                  <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs text-gray-400" aria-live="polite" id="char-counter">0 / 1000</span>
                    <button type="submit"
                            class="rounded-xl bg-brand-600 px-5 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">
                      Kirim
                    </button>
                  </div>
                </div>
              </div>
            </form>
            @else
            <div class="rounded-xl bg-gray-50 border border-gray-200 p-4 text-center text-sm text-gray-500">
              <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Login</a>
              untuk menulis komentar.
            </div>
            @endauth
          </div>
        </section>
      </article>

      {{-- ─── Sidebar ─────────────────────────────────────────────────────── --}}
      <aside class="lg:w-72 flex-shrink-0 space-y-5" aria-label="Sidebar artikel">

        {{-- Related Articles --}}
        @if($related->isNotEmpty())
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
          <h3 class="mb-4 text-sm font-extrabold uppercase tracking-wide text-gray-500">Artikel Terkait</h3>
          <div class="space-y-4">
            @foreach($related as $rel)
            <a href="{{ route('articles.show', $rel->slug) }}"
               class="group flex gap-3 focus:outline-none focus:ring-2 focus:ring-brand-600 rounded-lg"
               aria-label="{{ $rel->title }}">
              @if($rel->image_url)
              <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}"
                   class="h-16 w-16 flex-shrink-0 rounded-lg object-cover" loading="lazy">
              @else
              <div class="h-16 w-16 flex-shrink-0 rounded-lg bg-gray-100 flex items-center justify-center" aria-hidden="true">
                <svg class="h-7 w-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909"/></svg>
              </div>
              @endif
              <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover:text-brand-700 transition-colors leading-snug">
                  {{ $rel->title }}
                </p>
                <p class="mt-1 text-xs text-gray-400">{{ $rel->reading_time }} mnt · {{ number_format($rel->views) }} dibaca</p>
              </div>
            </a>
            @endforeach
          </div>
        </div>
        @endif

        {{-- Article Info --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
          <h3 class="mb-4 text-sm font-extrabold uppercase tracking-wide text-gray-500">Info Artikel</h3>
          <dl class="space-y-3 text-sm">
            <div class="flex items-center justify-between">
              <dt class="text-gray-500">Kategori</dt>
              <dd>
                <a href="{{ route('catalog', ['category' => $article->category_id]) }}"
                   class="rounded-full bg-brand-600/10 px-3 py-0.5 font-semibold text-brand-700 hover:bg-brand-600/20 transition">
                  {{ $article->category->name ?? 'Umum' }}
                </a>
              </dd>
            </div>
            <div class="flex items-center justify-between">
              <dt class="text-gray-500">Diterbitkan</dt>
              <dd class="font-semibold text-gray-800">{{ $article->created_at->translatedFormat('j M Y') }}</dd>
            </div>
            <div class="flex items-center justify-between">
              <dt class="text-gray-500">Waktu baca</dt>
              <dd class="font-semibold text-gray-800">{{ $article->reading_time }} menit</dd>
            </div>
            <div class="flex items-center justify-between">
              <dt class="text-gray-500">Dibaca</dt>
              <dd class="font-semibold text-gray-800">{{ number_format($article->views) }}×</dd>
            </div>
            <div class="flex items-center justify-between">
              <dt class="text-gray-500">Penulis</dt>
              <dd class="font-semibold text-gray-800 truncate max-w-[140px]">{{ $article->writer }}</dd>
            </div>
          </dl>
        </div>

        {{-- Back to catalog CTA --}}
        <a href="{{ route('catalog') }}"
           class="block rounded-2xl bg-ink-900 px-5 py-4 text-center text-sm font-semibold text-white hover:bg-ink-800 transition focus:outline-none focus:ring-2 focus:ring-ink-900">
          ← Kembali ke Katalog
        </a>
      </aside>
    </div>
  </div>
</main>

<script>
// Reading progress bar
(function() {
    const bar     = document.getElementById('reading-progress-bar');
    const content = document.getElementById('article-content');
    if (!bar || !content) return;

    function updateProgress() {
        const rect   = content.getBoundingClientRect();
        const start  = content.offsetTop;
        const end    = start + content.offsetHeight - window.innerHeight;
        const prog   = end > 0 ? Math.min(100, Math.max(0, ((window.scrollY - start) / end) * 100)) : 0;
        bar.style.width = prog + '%';
    }
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
})();

// Copy link
(function() {
    const btn   = document.getElementById('copy-link-btn');
    if (!btn) return;
    btn.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(btn.dataset.url);
            document.getElementById('copy-icon').classList.add('hidden');
            document.getElementById('check-icon').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('copy-icon').classList.remove('hidden');
                document.getElementById('check-icon').classList.add('hidden');
            }, 2000);
        } catch { }
    });
})();

// Bookmark toggle (AJAX)
(function() {
    const btn = document.getElementById('bookmark-btn');
    if (!btn) return;
    btn.addEventListener('click', async () => {
        try {
            const res  = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
            });
            const data = await res.json();
            const bookmarked = data.bookmarked;
            btn.dataset.bookmarked = bookmarked;
            btn.setAttribute('aria-pressed', bookmarked);
            btn.setAttribute('aria-label', bookmarked ? 'Hapus dari bookmark' : 'Tambah ke bookmark');
            document.getElementById('bookmark-label').textContent = bookmarked ? 'Tersimpan' : 'Simpan';
            const icon = document.getElementById('bookmark-icon');
            icon.setAttribute('fill', bookmarked ? 'currentColor' : 'none');
            btn.className = btn.className.replace(
                bookmarked ? 'border-gray-200 text-gray-600 hover:border-brand-300 hover:text-brand-700' : 'border-brand-600 bg-brand-600/10 text-brand-700',
                bookmarked ? 'border-brand-600 bg-brand-600/10 text-brand-700' : 'border-gray-200 text-gray-600 hover:border-brand-300 hover:text-brand-700'
            );
        } catch(e) { console.error(e); }
    });
})();

// Comment character counter
(function() {
    const ta  = document.getElementById('comment-input');
    const cnt = document.getElementById('char-counter');
    if (!ta || !cnt) return;
    ta.addEventListener('input', () => { cnt.textContent = ta.value.length + ' / 1000'; });
})();
</script>
</x-layouts.app>
