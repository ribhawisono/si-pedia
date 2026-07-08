<x-layouts.app title="Profil — SI-Pedia">
<div class="min-h-screen bg-profilebg">
  <main class="mx-auto max-w-[900px] px-4 sm:px-8 py-10">
    <div class="rounded-2xl bg-white px-6 sm:px-10 py-8 sm:py-10 shadow-sm">
      <div class="flex flex-col items-center">
        <div class="relative">
          <div class="grid h-24 w-24 place-items-center rounded-full bg-gradient-to-br from-fuchsia-500 to-purple-600 text-2xl font-bold text-white ring-4 ring-white shadow-lg overflow-hidden">
            @if($user->avatar)
                <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
            @else
                {{ substr($user->name, 0, 2) }}
            @endif
          </div>
        </div>
        <h1 class="mt-4 text-2xl font-extrabold text-ink-900">{{ $user->name }}</h1>
        <span class="mt-2 rounded-full bg-userbadge px-4 py-1 text-xs font-bold text-purple-700">{{ ucfirst($user->role) }}</span>
        <p class="mt-2 text-sm text-gray-400">{{ $user->email }}</p>
      </div>
      <hr class="my-6 border-gray-100">
      <div class="mx-auto grid max-w-2xl grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-5">
          <div><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Full Name</p><p class="mt-1 text-sm font-semibold text-ink-900">{{ $user->name }}</p></div>
          <div><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Email</p><p class="mt-1 text-sm font-semibold text-ink-900">{{ $user->email }}</p></div>
          <div><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Join</p><p class="mt-1 text-sm font-semibold text-ink-900">{{ $user->created_at->translatedFormat('F Y') }}</p></div>
          <div><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Study Program</p><p class="mt-1 text-sm font-semibold text-ink-900">{{ $user->study_program ?? '-' }}</p></div>
          <div><p class="text-xs font-bold uppercase tracking-wide text-gray-400">Force</p><p class="mt-1 text-sm font-semibold text-ink-900">{{ $user->force ?? '-' }}</p></div>
      </div>

      @if($user->role === 'dosen' && $user->lecturer)
      <div class="mx-auto mt-6 max-w-2xl rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm">
        <p class="font-bold text-blue-800 mb-2">Data Dosen</p>
        <dl class="grid grid-cols-2 gap-2 text-xs">
          <div><dt class="text-gray-500">NIDN</dt><dd class="font-mono font-semibold">{{ $user->lecturer->nidn ?? '—' }}</dd></div>
          <div><dt class="text-gray-500">Status</dt><dd class="font-semibold">{{ ucfirst($user->lecturer->status ?? '—') }}</dd></div>
        </dl>
      </div>
      @endif

      <div class="mx-auto mt-8 max-w-2xl">
        <a href="{{ route('profile.edit') }}" class="block text-center w-full rounded-xl bg-ink-900 py-3 text-sm font-bold text-white hover:bg-gray-800 transition">✎ Edit Profile</a>
      </div>
    </div>
  </main>
</div>
</x-layouts.app>
