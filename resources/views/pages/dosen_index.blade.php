<x-layouts.admin title="Data Dosen — SI-Pedia" section="dosen">
<main class="mx-auto max-w-[1200px] px-8 py-8">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Data Dosen</h1>
      <p class="mt-1 text-sm text-gray-500">Kelola data dosen yang terdaftar di sistem.</p>
    </div>
    <a href="{{ route('admin.dosen.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-brand-700 transition">
      + Tambah Dosen
    </a>
  </div>

  <div class="mt-6 flex items-center gap-3">
    <form action="{{ route('admin.dosen.index') }}" method="GET"
          class="flex flex-1 items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-2.5 shadow-sm focus-within:border-brand-600 transition">
      <span class="text-gray-400">🔍</span>
      <input type="text" name="q" value="{{ request('q') }}"
             placeholder="Cari dosen by NIDN, nama, atau alamat..."
             class="w-full text-sm text-gray-800 bg-transparent border-none focus:ring-0 p-0">
    </form>
  </div>

  @if(session('success'))
    <div class="mt-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif

  <div class="mt-5 overflow-hidden rounded-2xl border border-gray-200 shadow-sm bg-white">
    <div class="grid grid-cols-[50px_70px_150px_1fr_1fr_180px] gap-3 border-b border-gray-100 bg-tablehead px-5 py-3 text-xs font-bold text-gray-600 uppercase tracking-wide">
      <div>No</div>
      <div>Foto</div>
      <div>NIDN</div>
      <div>Nama / Email</div>
      <div>Alamat</div>
      <div>Aksi</div>
    </div>

    @forelse($lecturers as $i => $lecturer)
    <div class="grid grid-cols-[50px_70px_150px_1fr_1fr_180px] items-center gap-3 px-5 py-4 border-b border-gray-50 hover:bg-gray-50 transition">
      <div class="text-sm font-bold text-gray-500">
        {{ $i + 1 + ($lecturers->currentPage() - 1) * $lecturers->perPage() }}
      </div>
      <div>
        @if($lecturer->photo)
          <img src="{{ $lecturer->photo ? (str_starts_with($lecturer->photo, "http") ? $lecturer->photo : Storage::url($lecturer->photo)) : null }}" class="h-10 w-10 rounded-full object-cover shadow-sm">
        @else
          <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-sm text-gray-500 font-bold">
            {{ strtoupper(substr($lecturer->user->name ?? 'D', 0, 1)) }}
          </div>
        @endif
      </div>
      <div class="text-sm font-mono text-gray-700">{{ $lecturer->nidn ?? '-' }}</div>
      <div>
        <p class="text-sm font-bold text-gray-900">{{ $lecturer->user->name ?? '—' }}</p>
        <p class="text-xs text-gray-400">{{ $lecturer->user->email ?? '—' }}</p>
      </div>
      <div class="text-sm text-gray-600">{{ $lecturer->address ?? '-' }}</div>
      <div class="flex items-center gap-2">
        <a href="{{ route('admin.dosen.edit', $lecturer) }}"
           class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
          ✎ Edit
        </a>
        <form action="{{ route('admin.dosen.destroy', $lecturer) }}" method="POST"
              onsubmit="return confirm('Hapus dosen ini beserta akunnya?')">
          @csrf @method('DELETE')
          <button type="submit"
                  class="rounded-lg border border-red-300 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 transition">
            🗑 Hapus
          </button>
        </form>
      </div>
    </div>
    @empty
    <div class="px-5 py-12 text-center text-sm text-gray-400">
      Belum ada data dosen. <a href="{{ route('admin.dosen.create') }}" class="text-brand-600 font-semibold">Tambah sekarang →</a>
    </div>
    @endforelse
  </div>

  <div class="mt-5 flex items-center justify-between text-sm text-gray-500">
    <span>Menampilkan {{ $lecturers->firstItem() ?? 0 }}–{{ $lecturers->lastItem() ?? 0 }} dari {{ $lecturers->total() }} dosen</span>
    {{ $lecturers->links() }}
  </div>
</main>
</x-layouts.admin>
