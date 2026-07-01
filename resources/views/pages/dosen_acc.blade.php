<x-layouts.admin title="Detail Dosen — SI-Pedia" section="dosen">
<main class="mx-auto max-w-[1100px] px-8 py-8">
  <div class="flex items-center gap-3 mb-1">
    <a href="{{ route('admin.dosen.index') }}" class="text-gray-400 hover:text-gray-700 transition text-2xl">←</a>
    <h1 class="text-3xl font-extrabold text-gray-900">Detail Dosen</h1>
  </div>
  <p class="ml-9 text-sm text-gray-500 mb-6">Verifikasi dan kelola data dosen terdaftar.</p>

  <div class="grid grid-cols-1 lg:grid-cols-[1.8fr_1fr] gap-6">
    {{-- Kiri: Data dosen --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-7 shadow-sm space-y-5">
      <h2 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3">Data Dosen</h2>

      <div class="flex items-center gap-4">
        @if($lecturer->photo)
          <img src="{{ $lecturer->photo ? (str_starts_with($lecturer->photo, "http") ? $lecturer->photo : Storage::url($lecturer->photo)) : null }}" class="h-16 w-16 rounded-full object-cover shadow">
        @else
          <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-500">
            {{ strtoupper(substr($lecturer->user->name ?? 'D', 0, 1)) }}
          </div>
        @endif
        <div>
          <p class="font-bold text-gray-900 text-lg">{{ $lecturer->user->name ?? '—' }}</p>
          <p class="text-sm text-gray-400">{{ $lecturer->user->email ?? '—' }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-400 mb-1">NIDN</label>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-mono text-gray-700">
            {{ $lecturer->nidn ?? '—' }}
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 mb-1">NIP</label>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-mono text-gray-700">
            {{ $lecturer->nip ?? '—' }}
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 mb-1">Tempat Lahir</label>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
            {{ $lecturer->place_of_birth ?? '—' }}
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 mb-1">Tanggal Lahir</label>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
            {{ $lecturer->date_of_birth ? \Carbon\Carbon::parse($lecturer->date_of_birth)->translatedFormat('j F Y') : '—' }}
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 mb-1">No. Telepon</label>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
            {{ $lecturer->phone ?? '—' }}
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-400 mb-1">Gender</label>
          <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
            {{ $lecturer->gender ?? '—' }}
          </div>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-gray-400 mb-1">Program Studi</label>
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
          {{ $lecturer->study_program ?? 'Sistem Informasi' }}
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold text-gray-400 mb-1">Alamat</label>
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-700">
          {{ $lecturer->address ?? '—' }}
        </div>
      </div>
    </div>

    {{-- Kanan: Verifikasi --}}
    <div class="space-y-4">
      <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="bg-tablehead px-5 py-3 text-sm font-bold text-gray-800">Informasi Verifikasi</div>
        <div class="p-5 space-y-4 text-sm">
          <div>
            <p class="text-xs font-bold text-gray-400 mb-1">Terdaftar Sejak</p>
            <p class="font-semibold text-gray-800">
              {{ $lecturer->created_at->translatedFormat('j F Y') }}
            </p>
          </div>
          <div>
            <p class="text-xs font-bold text-gray-400 mb-1">Status Saat Ini</p>
            @php
              $statusClass = match($lecturer->status) {
                'active'   => 'bg-green-100 text-green-700',
                'rejected' => 'bg-red-100 text-red-700',
                default    => 'bg-yellow-100 text-yellow-700',
              };
              $statusLabel = match($lecturer->status) {
                'active'   => '✅ Aktif',
                'rejected' => '❌ Ditolak',
                default    => '⏳ Menunggu Verifikasi',
              };
            @endphp
            <span class="inline-block rounded-full px-3 py-1 text-xs font-bold {{ $statusClass }}">
              {{ $statusLabel }}
            </span>
          </div>
          <div>
            <p class="text-xs font-bold text-gray-400 mb-1">Akun Terhubung</p>
            <p class="text-gray-800">{{ $lecturer->user->email ?? '—' }}</p>
          </div>
        </div>
      </div>

      @if($lecturer->status === 'waiting')
      <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-5 space-y-3">
        <p class="text-sm font-bold text-yellow-800">Tindakan Verifikasi</p>
        <p class="text-xs text-yellow-700">Setujui atau tolak pendaftaran dosen ini.</p>
        <div class="flex gap-3">
          <form action="{{ route('admin.dosen.destroy', $lecturer) }}" method="POST"
                onsubmit="return confirm('Tolak dan hapus dosen ini?')" class="flex-1">
            @csrf @method('DELETE')
            <button type="submit"
                    class="w-full rounded-xl border border-red-300 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 transition">
              ❌ Tolak
            </button>
          </form>
          <form action="{{ route('admin.dosen.approve', $lecturer) }}" method="POST" class="flex-1">
            @csrf @method('PATCH')
            <button type="submit"
                    class="w-full rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition">
              ✅ Setujui
            </button>
          </form>
        </div>
      </div>
      @endif

      <a href="{{ route('admin.dosen.edit', $lecturer) }}"
         class="block text-center rounded-2xl border border-gray-200 bg-white py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
        ✎ Edit Data Dosen
      </a>
    </div>
  </div>
</main>
</x-layouts.admin>
