<x-layouts.app title="Manage Users — SI-Pedia">
<main class="mx-auto max-w-[1200px] px-8 py-8">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Manage Users</h1>
      <p class="mt-1 text-sm text-gray-500">Kelola akun pengguna di sistem.</p>
    </div>
    <div class="flex gap-3">
      <a href="{{ route('admin.account-reports.index') }}"
         class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-100 transition">
        🚩 Laporan Akun
        @php $pendingReports = \App\Models\AccountReport::where('status','pending')->count(); @endphp
        @if($pendingReports > 0)
          <span class="rounded-full bg-red-500 px-2 py-0.5 text-xs font-black text-white">{{ $pendingReports }}</span>
        @endif
      </a>
      <a href="{{ route('admin.panel') }}" class="rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-200 transition">
        ← Panel
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="mt-5 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm font-semibold text-green-700">
      ✅ {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mt-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm font-semibold text-red-700">
      ⚠️ {{ session('error') }}
    </div>
  @endif

  <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-left">
      <thead>
        <tr class="bg-tablehead text-xs font-bold uppercase tracking-wide text-gray-600">
          <th class="py-3.5 px-5">Pengguna</th>
          <th class="py-3.5 px-5">Email</th>
          <th class="py-3.5 px-5">Role</th>
          <th class="py-3.5 px-5">Artikel</th>
          <th class="py-3.5 px-5">Bergabung</th>
          <th class="py-3.5 px-5">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @foreach($users as $user)
        <tr class="hover:bg-gray-50 transition-colors">
          <td class="py-4 px-5">
            <div class="flex items-center gap-3">
              @if($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" class="h-9 w-9 rounded-full object-cover">
              @else
                <div class="h-9 w-9 rounded-full flex items-center justify-center font-bold text-sm
                  {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' :
                     ($user->role === 'dosen' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                  {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
              @endif
              <span class="text-sm font-bold text-gray-900">{{ $user->name }}</span>
            </div>
          </td>
          <td class="py-4 px-5 text-sm text-gray-500">{{ $user->email }}</td>
          <td class="py-4 px-5">
            <span class="rounded-full px-3 py-1 text-xs font-bold
              {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' :
                 ($user->role === 'dosen' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
              {{ ucfirst($user->role) }}
            </span>
          </td>
          <td class="py-4 px-5 text-sm text-gray-500">
            {{ $user->articles_count ?? 0 }}
          </td>
          <td class="py-4 px-5 text-sm text-gray-400">{{ $user->created_at->translatedFormat('j M Y') }}</td>
          <td class="py-4 px-5">
            @if($user->id !== auth()->id())
            <div class="flex items-center gap-2">
              {{-- Ubah role --}}
              <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" class="flex items-center gap-1">
                @csrf @method('PATCH')
                <select name="role" onchange="this.form.submit()"
                        class="rounded-lg border border-gray-200 text-xs font-semibold text-gray-700 px-2 py-1.5 focus:ring-0 focus:border-brand-600 cursor-pointer">
                  <option value="user"  @selected($user->role === 'user')>User</option>
                  <option value="dosen" @selected($user->role === 'dosen')>Dosen</option>
                  <option value="admin" @selected($user->role === 'admin')>Admin</option>
                </select>
              </form>
              {{-- Report --}}
              <a href="{{ route('users.report', $user) }}"
                 class="rounded-lg bg-red-50 border border-red-200 px-2.5 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition"
                 title="Laporkan akun ini">
                🚩
              </a>
            </div>
            @else
            <span class="text-xs text-gray-400 italic">Akun kamu</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-5">{{ $users->links() }}</div>
</main>
</x-layouts.app>
