<x-layouts.app :title="($lecturer->user->name ?? 'Dosen') . ' — SI-Pedia'" footer="full"
               :meta_description="'Profil dosen ' . ($lecturer->user->name ?? '') . ', Program Studi Sistem Informasi Unindra.'">

<div class="bg-ink-900 py-10">
  <div class="mx-auto max-w-[1100px] px-4 sm:px-6 lg:px-8">
    <nav class="mb-3 flex items-center gap-2 text-xs text-white/50" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
      <span aria-hidden="true">›</span>
      <a href="{{ route('dosen.public.index') }}" class="hover:text-white transition">Dosen</a>
      <span aria-hidden="true">›</span>
      <span class="text-white">{{ $lecturer->user->name ?? 'Profil' }}</span>
    </nav>
  </div>
</div>

<main class="bg-gray-50 min-h-[60vh]" id="main-content">
  <div class="mx-auto max-w-[1100px] px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid gap-6 lg:grid-cols-[280px_1fr]">

      {{-- Profile card --}}
      <div class="space-y-4">
        <div class="card p-6 text-center">
          <div class="mx-auto mb-4 h-24 w-24 overflow-hidden rounded-full bg-gray-100 ring-4 ring-white shadow-md">
            <img src="{{ $lecturer->photo ? (str_starts_with($lecturer->photo,'http') ? $lecturer->photo : Storage::url($lecturer->photo)) : 'https://ui-avatars.com/api/?name='.urlencode($lecturer->user->name??'Dosen').'&background=336cbc&color=fff&size=96' }}"
                 alt="Foto {{ $lecturer->user->name ?? 'Dosen' }}" class="h-full w-full object-cover">
          </div>
          <h1 class="text-base font-bold text-gray-900">{{ $lecturer->user->name ?? '—' }}</h1>
          <p class="text-xs text-gray-500 mt-1">Dosen Sistem Informasi</p>
          <span class="mt-2 inline-block rounded-full bg-green-100 px-3 py-0.5 text-xs font-semibold text-green-700">Aktif</span>
        </div>

        <div class="card">
          <div class="card-header">Informasi</div>
          <div class="card-body">
            <dl class="space-y-3 text-xs">
              @if($lecturer->nidn)
              <div>
                <dt class="text-gray-400 font-semibold">NIDN</dt>
                <dd class="font-mono text-gray-800 mt-0.5">{{ $lecturer->nidn }}</dd>
              </div>
              @endif
              @if($lecturer->nip)
              <div>
                <dt class="text-gray-400 font-semibold">NIP</dt>
                <dd class="font-mono text-gray-800 mt-0.5">{{ $lecturer->nip }}</dd>
              </div>
              @endif
              @if($lecturer->address)
              <div>
                <dt class="text-gray-400 font-semibold">Alamat</dt>
                <dd class="text-gray-800 mt-0.5 leading-relaxed">{{ $lecturer->address }}</dd>
              </div>
              @endif
              <div>
                <dt class="text-gray-400 font-semibold">Prodi</dt>
                <dd class="text-gray-800 mt-0.5">Sistem Informasi</dd>
              </div>
              <div>
                <dt class="text-gray-400 font-semibold">Bergabung</dt>
                <dd class="text-gray-800 mt-0.5">{{ $lecturer->created_at->translatedFormat('Y') }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      {{-- Articles by this lecturer --}}
      <div>
        <div class="mb-5 flex items-center justify-between">
          <h2 class="text-base font-bold text-gray-900">
            Artikel oleh {{ $lecturer->user->name ?? 'Dosen ini' }}
            <span class="ml-2 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500">{{ $articles->count() }}</span>
          </h2>
          @if($articles->count() > 0)
          <a href="{{ route('catalog', ['q' => $lecturer->user->name ?? '']) }}"
             class="text-xs font-semibold text-brand-600 hover:text-brand-700">Lihat semua →</a>
          @endif
        </div>

        @forelse($articles as $article)
        <x-article-card :article="$article" variant="list" class="mb-4"/>
        @empty
        <x-empty-state title="Belum ada artikel" description="Dosen ini belum mempublikasikan artikel." :action="route('catalog')"/>
        @endforelse
      </div>

    </div>
  </div>
</main>
</x-layouts.app>
