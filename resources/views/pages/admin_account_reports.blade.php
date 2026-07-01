<x-layouts.app title="Report Akun — SI-Pedia">
<main class="mx-auto max-w-[1200px] px-8 py-10">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-4xl font-extrabold text-gray-900">Laporan Akun</h1>
      <p class="mt-1 text-gray-500 text-sm">Tinjau dan kelola laporan akun dari pengguna.</p>
    </div>
    <a href="{{ route('admin.panel') }}" class="rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-200 transition">
      ← Admin Panel
    </a>
  </div>

  {{-- Stats --}}
  <div class="mt-8 grid grid-cols-3 gap-4">
    <div class="rounded-2xl border border-yellow-200 bg-yellow-50 px-6 py-5 text-center">
      <p class="text-3xl font-black text-yellow-600">{{ $counts['pending'] }}</p>
      <p class="mt-1 text-sm font-bold text-yellow-700">Menunggu Tinjauan</p>
    </div>
    <div class="rounded-2xl border border-green-200 bg-green-50 px-6 py-5 text-center">
      <p class="text-3xl font-black text-green-600">{{ $counts['reviewed'] }}</p>
      <p class="mt-1 text-sm font-bold text-green-700">Sudah Ditinjau</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-gray-50 px-6 py-5 text-center">
      <p class="text-3xl font-black text-gray-500">{{ $counts['dismissed'] }}</p>
      <p class="mt-1 text-sm font-bold text-gray-500">Diabaikan</p>
    </div>
  </div>

  {{-- Filter --}}
  <div class="mt-6 flex gap-3">
    @foreach(['semua' => '', 'pending' => 'pending', 'reviewed' => 'reviewed', 'dismissed' => 'dismissed'] as $label => $val)
    <a href="{{ route('admin.account-reports.index', $val ? ['status' => $val] : []) }}"
       class="rounded-lg px-4 py-2 text-sm font-semibold transition
              {{ request('status', '') === $val ? 'bg-ink-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
      {{ ucfirst($label) }}
    </a>
    @endforeach
  </div>

  @if(session('success'))
    <div class="mt-5 rounded-xl bg-green-50 border border-green-200 px-5 py-3 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif

  {{-- List laporan --}}
  <div class="mt-6 space-y-4">
    @forelse($reports as $report)
    <div class="rounded-2xl border bg-white shadow-sm overflow-hidden
                {{ $report->status === 'pending' ? 'border-yellow-200' : ($report->status === 'reviewed' ? 'border-green-200' : 'border-gray-200') }}">
      <div class="px-6 py-5">
        <div class="flex items-start justify-between gap-4">
          <div class="flex-1">
            {{-- Header --}}
            <div class="flex items-center gap-3 flex-wrap">
              <span class="rounded-full px-3 py-1 text-xs font-bold
                {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                   ($report->status === 'reviewed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600') }}">
                {{ ucfirst($report->status) }}
              </span>
              <span class="text-xs text-gray-400">{{ $report->created_at->translatedFormat('j F Y, H:i') }}</span>
            </div>

            {{-- Pelaporkan → Terlapor --}}
            <div class="mt-3 flex items-center gap-3">
              <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700">
                  {{ strtoupper(substr($report->reporter->name ?? '?', 0, 1)) }}
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900">{{ $report->reporter->name ?? 'Akun dihapus' }}</p>
                  <p class="text-xs text-gray-400">Pelapor</p>
                </div>
              </div>
              <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
              <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center text-xs font-bold text-red-700">
                  {{ strtoupper(substr($report->reportedUser->name ?? '?', 0, 1)) }}
                </div>
                <div>
                  <p class="text-sm font-bold text-gray-900">{{ $report->reportedUser->name ?? 'Akun dihapus' }}</p>
                  <p class="text-xs text-gray-400">Terlapor · {{ ucfirst($report->reportedUser->role ?? '-') }}</p>
                </div>
              </div>
            </div>

            {{-- Alasan & Deskripsi --}}
            <div class="mt-3">
              <span class="inline-block rounded-md bg-red-50 border border-red-200 px-3 py-1 text-xs font-bold text-red-700">
                {{ $report->reason }}
              </span>
              @if($report->description)
                <p class="mt-2 text-sm text-gray-600">{{ $report->description }}</p>
              @endif
            </div>

            @if($report->admin_note)
              <div class="mt-3 rounded-lg bg-gray-50 border border-gray-200 px-4 py-2 text-xs text-gray-600">
                <span class="font-bold">Catatan Admin:</span> {{ $report->admin_note }}
              </div>
            @endif
          </div>

          {{-- Action --}}
          @if($report->status === 'pending')
          <div class="flex-shrink-0">
            <form action="{{ route('admin.account-reports.update', $report) }}" method="POST"
                  class="space-y-2 min-w-[180px]">
              @csrf @method('PATCH')
              <textarea name="admin_note" rows="2" placeholder="Catatan (opsional)"
                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-700 resize-none focus:ring-0 focus:border-gray-400"></textarea>
              <div class="flex gap-2">
                <button type="submit" name="status" value="reviewed"
                        class="flex-1 rounded-lg bg-green-500 py-2 text-xs font-bold text-white hover:bg-green-600 transition">
                  ✅ Tinjau
                </button>
                <button type="submit" name="status" value="dismissed"
                        class="flex-1 rounded-lg bg-gray-200 py-2 text-xs font-bold text-gray-700 hover:bg-gray-300 transition">
                  Abaikan
                </button>
              </div>
            </form>
          </div>
          @endif
        </div>
      </div>
    </div>
    @empty
    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-16 text-center">
      <p class="text-4xl mb-3">📭</p>
      <p class="font-semibold text-gray-500">Tidak ada laporan akun.</p>
    </div>
    @endforelse
  </div>

  <div class="mt-6">{{ $reports->links() }}</div>
</main>
</x-layouts.app>
