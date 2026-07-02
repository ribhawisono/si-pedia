<x-layouts.app :title="$user->name . ' — SI-Pedia'" footer="min"
               :meta_description="'Profil penulis ' . $user->name . ' di SI-Pedia.'">

<div class="bg-ink-900 py-10">
  <div class="mx-auto max-w-[1100px] px-4 sm:px-6 lg:px-8">
    <nav class="mb-3 flex items-center gap-2 text-xs text-white/50" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
      <span aria-hidden="true">›</span>
      <span class="text-white">{{ $user->name }}</span>
    </nav>
  </div>
</div>

<main class="bg-gray-50 min-h-[60vh]" id="main-content">
  <div class="mx-auto max-w-[1100px] px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid gap-6 lg:grid-cols-[260px_1fr]">

      {{-- Profile card --}}
      <div class="space-y-4">
        <div class="card p-6 text-center">
          <div class="mx-auto mb-4 h-20 w-20 overflow-hidden rounded-full bg-gray-100 ring-4 ring-white shadow">
            <img src="{{ $user->avatar_url }}" alt="Foto {{ $user->name }}" class="h-full w-full object-cover">
          </div>
          <h1 class="text-sm font-bold text-gray-900">{{ $user->name }}</h1>
          <p class="mt-1 text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
          @if($user->study_program)
          <p class="mt-1 text-xs text-gray-400">{{ $user->study_program }}</p>
          @endif
        </div>

        <div class="card">
          <div class="card-header">Statistik</div>
          <div class="card-body">
            <dl class="grid grid-cols-2 gap-3 text-center text-xs">
              <div class="rounded-lg bg-gray-50 p-3">
                <dd class="text-xl font-black text-brand-700">{{ $articles->total() }}</dd>
                <dt class="text-gray-500 mt-0.5">Artikel</dt>
              </div>
              <div class="rounded-lg bg-gray-50 p-3">
                <dd class="text-xl font-black text-brand-700">{{ $user->created_at->translatedFormat('Y') }}</dd>
                <dt class="text-gray-500 mt-0.5">Bergabung</dt>
              </div>
            </dl>
          </div>
        </div>

        @auth
        @if(auth()->id() !== $user->id)
        <a href="{{ route('users.report', $user) }}"
           class="btn btn-ghost w-full justify-center text-red-500 border-red-200">
          🚩 Laporkan Akun
        </a>
        @endif
        @endauth
      </div>

      {{-- Articles --}}
      <div>
        <h2 class="text-sm font-bold text-gray-700 mb-4">
          Artikel oleh {{ $user->name }}
          <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">{{ $articles->total() }}</span>
        </h2>

        @forelse($articles as $article)
        <div class="mb-4"><x-article-card :article="$article" variant="list"/></div>
        @empty
        <x-empty-state title="Belum ada artikel" description="Pengguna ini belum mempublikasikan artikel."
                       :action="route('catalog')"/>
        @endforelse

        @if($articles->hasPages())
        <div class="mt-6">{{ $articles->links() }}</div>
        @endif
      </div>
    </div>
  </div>
</main>
</x-layouts.app>
