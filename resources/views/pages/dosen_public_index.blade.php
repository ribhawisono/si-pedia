<x-layouts.app title="Dosen — SI-Pedia" active="About us" footer="full"
               meta_description="Daftar dosen Program Studi Sistem Informasi Universitas Indraprasta PGRI.">

<div class="bg-ink-900 py-10">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">
    <nav class="mb-3 flex items-center gap-2 text-xs text-white/50" aria-label="Breadcrumb">
      <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
      <span aria-hidden="true">›</span>
      <a href="{{ route('about') }}" class="hover:text-white transition">Tentang</a>
      <span aria-hidden="true">›</span>
      <span class="text-white">Dosen</span>
    </nav>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Tim Dosen</h1>
    <p class="mt-1 text-sm text-white/60">Program Studi Sistem Informasi — Universitas Indraprasta PGRI</p>
  </div>
</div>

<main class="bg-gray-50 py-10" id="main-content">
  <div class="mx-auto max-w-[1440px] px-4 sm:px-6 lg:px-8">

    @if($lecturers->isEmpty())
      <x-empty-state title="Belum ada dosen terdaftar" description="Data dosen sedang disiapkan."
                     action="{{ route('home') }}" actionLabel="Kembali ke Beranda"/>
    @else
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      @foreach($lecturers as $lecturer)
      <a href="{{ route('dosen.public.show', $lecturer) }}"
         class="group card hover-lift p-5 flex flex-col items-center text-center focus:outline-none focus:ring-2 focus:ring-brand-600">
        <div class="mb-3 h-20 w-20 overflow-hidden rounded-full bg-gray-100 ring-2 ring-white shadow-md">
          <img src="{{ $lecturer->photo ? (str_starts_with($lecturer->photo,'http') ? $lecturer->photo : Storage::url($lecturer->photo)) : 'https://ui-avatars.com/api/?name='.urlencode($lecturer->user->name??'Dosen').'&background=336cbc&color=fff&size=80' }}"
               alt="Foto {{ $lecturer->user->name ?? 'Dosen' }}"
               class="h-full w-full object-cover" loading="lazy">
        </div>
        <h2 class="text-sm font-bold text-gray-900 group-hover:text-brand-700 transition-colors leading-snug">
          {{ $lecturer->user->name ?? '—' }}
        </h2>
        @if($lecturer->nidn)
        <p class="mt-1 text-xs text-gray-400 font-mono">NIDN {{ $lecturer->nidn }}</p>
        @endif
        @if($lecturer->address)
        <p class="mt-1 text-xs text-gray-500 line-clamp-1">{{ $lecturer->address }}</p>
        @endif
        <span class="mt-3 text-xs font-semibold text-brand-600 group-hover:underline">Lihat Profil →</span>
      </a>
      @endforeach
    </div>
    @endif

  </div>
</main>
</x-layouts.app>
