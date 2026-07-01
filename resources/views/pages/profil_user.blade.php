<x-layouts.app title="Profil — SI-Pedia">
<div class="min-h-screen bg-profilebg">
  <main class="mx-auto max-w-[1320px] px-8 py-12">
    <div class="rounded-3xl bg-white px-10 py-12 shadow-sm">
      <div class="flex flex-col items-center">
        <div class="relative">
          <div class="grid h-32 w-32 place-items-center rounded-full bg-gradient-to-br from-fuchsia-500 to-purple-600 text-4xl font-bold text-white ring-4 ring-white shadow-lg overflow-hidden">
            @if($user->avatar)
                <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover">
            @else
                {{ substr($user->name, 0, 2) }}
            @endif
          </div>
        </div>
        <h1 class="mt-5 text-4xl font-extrabold text-ink-900">{{ $user->name }}</h1>
        <span class="mt-3 rounded-full bg-userbadge px-5 py-1 text-sm font-bold text-purple-700">{{ ucfirst($user->role) }}</span>
        <p class="mt-2 text-xl text-gray-300">{{ $user->email }}</p>
      </div>
      <hr class="my-8 border-gray-100">
      <div class="mx-auto grid max-w-3xl grid-cols-2 gap-x-20 gap-y-8">
          <div><p class="text-lg font-bold text-gray-400">Full Name</p><p class="mt-1 text-lg text-ink-900">{{ $user->name }}</p></div>
          <div><p class="text-lg font-bold text-gray-400">Email</p><p class="mt-1 text-lg text-ink-900">{{ $user->email }}</p></div>
          <div><p class="text-lg font-bold text-gray-400">Email</p><p class="mt-1 text-lg text-ink-900">{{ $user->email }}</p></div>
          <div><p class="text-lg font-bold text-gray-400">Join</p><p class="mt-1 text-lg text-ink-900">{{ $user->created_at->translatedFormat('F Y') }}</p></div>
          <div><p class="text-lg font-bold text-gray-400">Study Program</p><p class="mt-1 text-lg text-ink-900">{{ $user->study_program ?? '-' }}</p></div>
          <div><p class="text-lg font-bold text-gray-400">Force</p><p class="mt-1 text-lg text-ink-900">{{ $user->force ?? '-' }}</p></div>
      </div>
      <div class="mx-auto mt-12 max-w-3xl">
        <a href="{{ route('profile.edit') }}" class="block text-center w-full rounded-2xl bg-ink-900 py-5 text-xl font-bold text-white hover:bg-gray-800 transition">✎ Edit Profile</a>
      </div>
    </div>
  </main>
</div>
</x-layouts.app>
