<x-layouts.app title="Artikel Saya — SI-Pedia">
<main class="mx-auto max-w-[1100px] px-8 py-10">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-5xl font-extrabold">Artikel Saya</h1>
      <p class="mt-2 text-gray-500">Kelola semua artikel yang pernah kamu tulis.</p>
    </div>
    <a href="{{ route('articles.create') }}"
       class="rounded-xl bg-brand-600 px-6 py-3 text-sm font-bold text-white shadow hover:bg-brand-700 transition">
      ✏️ Tulis Artikel Baru
    </a>
  </div>

  @if(session('success'))
    <div class="mt-6 rounded-xl bg-green-50 border border-green-200 px-5 py-4 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mt-6 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm font-semibold text-red-700">
      ⚠️ {{ session('error') }}
    </div>
  @endif

  {{-- Status legend --}}
  <div class="mt-6 flex flex-wrap gap-3 text-xs font-semibold">
    <span class="rounded-full bg-green-500 px-3 py-1 text-white">Active = Publik</span>
    <span class="rounded-full bg-yellow-400 px-3 py-1 text-white">Pending = Menunggu Persetujuan</span>
    <span class="rounded-full bg-gray-400 px-3 py-1 text-white">Draft = Tersimpan</span>
    <span class="rounded-full bg-red-500 px-3 py-1 text-white">Pending Delete = Request Hapus Dikirim</span>
  </div>

  <div class="mt-6 space-y-4">
    @forelse($articles as $article)
    <div class="rounded-2xl bg-white shadow-sm border border-gray-100 px-6 py-5 flex items-center gap-5">

      {{-- Thumbnail --}}
      <div class="flex-shrink-0">
        @if($article->image)
          <img src="{{ Storage::url($article->image) }}" class="h-20 w-24 rounded-lg object-cover">
        @else
          <div class="h-20 w-24 rounded-lg bg-gray-100 flex items-center justify-center text-2xl">📄</div>
        @endif
      </div>

      {{-- Info --}}
      <div class="flex-1 min-w-0">
        <h2 class="text-lg font-extrabold text-gray-900 truncate">{{ $article->title }}</h2>
        <p class="mt-1 text-sm text-gray-500">
          {{ $article->category->name ?? 'Tanpa Kategori' }} ·
          {{ \Carbon\Carbon::parse($article->created_at)->translatedFormat('j F Y') }}
        </p>
        @if($article->status === 'pending_delete')
          <p class="mt-1 text-xs text-red-500 font-semibold">⏳ Permintaan hapus sedang menunggu keputusan admin.</p>
        @elseif($article->status === 'pending')
          <p class="mt-1 text-xs text-yellow-600 font-semibold">⏳ Artikel sedang menunggu persetujuan admin untuk dipublikasikan.</p>
        @endif
      </div>

      {{-- Status badge --}}
      <div class="flex-shrink-0">
        @php
          $badgeClass = match($article->status) {
            'active'         => 'bg-green-500',
            'pending'        => 'bg-yellow-400',
            'pending_delete' => 'bg-red-500',
            default          => 'bg-gray-400',
          };
          $badgeLabel = match($article->status) {
            'active'         => 'Active',
            'pending'        => 'Pending',
            'pending_delete' => 'Pending Delete',
            default          => 'Draft',
          };
        @endphp
        <span class="rounded-full {{ $badgeClass }} px-4 py-1 text-xs font-bold text-white">{{ $badgeLabel }}</span>
      </div>

      {{-- Actions --}}
      <div class="flex-shrink-0 flex gap-2">
        @if($article->status === 'active')
          <a href="{{ route('articles.show', $article->slug) }}"
             class="rounded-lg bg-gray-100 px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-200 transition">
            👁 Lihat
          </a>
        @endif

        @if(in_array($article->status, ['draft', 'pending']))
          <a href="{{ route('articles.edit', $article) }}"
             class="rounded-lg bg-blue-50 px-4 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100 transition">
            ✏️ Edit
          </a>
        @endif

        @if(!in_array($article->status, ['pending_delete']))
          <form action="{{ route('articles.requestDelete', $article) }}" method="POST"
                onsubmit="return confirm('Kirim permintaan hapus artikel ini ke admin?')">
            @csrf @method('PATCH')
            <button type="submit"
                    class="rounded-lg bg-red-50 px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-100 transition">
              🗑 Request Hapus
            </button>
          </form>
        @endif
      </div>
    </div>
    @empty
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-16 text-center">
      <p class="text-5xl mb-4">📝</p>
      <p class="text-xl font-bold text-gray-700">Belum ada artikel</p>
      <p class="mt-2 text-gray-400">Mulai tulis artikel pertamamu sekarang!</p>
      <a href="{{ route('articles.create') }}"
         class="mt-6 inline-block rounded-xl bg-brand-600 px-8 py-3 text-sm font-bold text-white hover:bg-brand-700 transition">
        ✏️ Tulis Sekarang
      </a>
    </div>
    @endforelse
  </div>

  <div class="mt-6">{{ $articles->links() }}</div>
</main>
</x-layouts.app>
