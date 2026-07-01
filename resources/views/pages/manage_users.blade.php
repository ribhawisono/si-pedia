<x-layouts.app title="Manage Users — SI-Pedia">
<main class="mx-auto max-w-[1200px] px-8 py-8">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Manage Users</h1>
      <p class="mt-1 text-sm text-gray-500">Kelola seluruh akun pengguna di sistem.</p>
    </div>
    <div class="flex gap-3">
      <a href="{{ route('admin.account-reports.index') }}"
         class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-100 transition">
        🚩 Laporan
        @php $pr = \App\Models\AccountReport::where('status','pending')->count(); @endphp
        @if($pr > 0)<span class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-black text-white">{{ $pr }}</span>@endif
      </a>
      <a href="{{ route('admin.users.create') }}"
         class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-brand-700 transition">
        + Tambah User
      </a>
    </div>
  </div>

  @foreach(['success','error'] as $key)
    @if(session($key))
      <div class="mt-5 rounded-xl {{ $key === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }} border px-4 py-3 text-sm font-semibold">
        {{ session($key) }}
      </div>
    @endif
  @endforeach

  <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left text-sm">
      <thead>
        <tr class="bg-tablehead border-b border-gray-100 text-xs font-bold uppercase tracking-wide text-gray-500">
          <th class="px-5 py-3.5">Pengguna</th>
          <th class="px-5 py-3.5">Email</th>
          <th class="px-5 py-3.5">Role</th>
          <th class="px-5 py-3.5 text-center">Artikel</th>
          <th class="px-5 py-3.5">Bergabung</th>
          <th class="px-5 py-3.5">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($users as $user)
        <tr class="hover:bg-gray-50 transition-colors">
          <td class="px-5 py-4">
            <div class="flex items-center gap-3">
              <img src="{{ $user->avatar_url }}" class="h-9 w-9 rounded-full object-cover flex-shrink-0">
              <span class="font-bold text-gray-900">{{ $user->name }}</span>
            </div>
          </td>
          <td class="px-5 py-4 text-gray-500">{{ $user->email }}</td>
          <td class="px-5 py-4">
            <span class="rounded-full px-3 py-1 text-xs font-bold
              {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' :
                 ($user->role === 'dosen' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
              {{ ucfirst($user->role) }}
            </span>
          </td>
          <td class="px-5 py-4 text-center font-semibold text-gray-600">{{ $user->articles_count }}</td>
          <td class="px-5 py-4 text-gray-400 text-xs">{{ $user->created_at->translatedFormat('j M Y') }}</td>
          <td class="px-5 py-4">
            <div class="flex items-center gap-2">
              <a href="{{ route('admin.users.edit', $user) }}"
                 class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
                ✎ Edit
              </a>
              @if($user->id !== auth()->id())
              <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                    onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                @csrf @method('DELETE')
                <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-50 transition">
                  🗑 Hapus
                </button>
              </form>
              <a href="{{ route('users.report', $user) }}"
                 class="rounded-lg border border-orange-200 px-2.5 py-1.5 text-xs font-bold text-orange-600 hover:bg-orange-50 transition"
                 title="Laporkan akun">🚩</a>
              @else
              <span class="text-xs text-gray-300 italic">Akun kamu</span>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Belum ada user.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-5">{{ $users->links() }}</div>
</main>
</x-layouts.app>
