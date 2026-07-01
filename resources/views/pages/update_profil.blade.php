<x-layouts.app title="Update Profil — SI-Pedia" footer="min">
<main class="mx-auto max-w-[640px] px-4 sm:px-6 py-10" id="main-content">
  <div class="mb-6 flex items-center gap-3">
    <a href="{{ route('profile.show') }}" class="rounded-lg border border-gray-200 p-2 text-gray-500 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-brand-600" aria-label="Kembali ke profil">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    </a>
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Update Profil</h1>
      <p class="text-sm text-gray-500">Perbarui informasi akun kamu.</p>
    </div>
  </div>

  @if($errors->any())
  <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3" role="alert">
    <ul class="text-sm text-red-600 space-y-1">
      @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
    </ul>
  </div>
  @endif

  <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
        class="space-y-5 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm" data-validate>
    @csrf @method('PUT')

    {{-- Avatar --}}
    <div>
      <label class="mb-3 block text-sm font-bold text-gray-700">Foto Profil</label>
      <div class="flex items-end gap-4">
        <div class="h-20 w-20 overflow-hidden rounded-full bg-gray-200 flex-shrink-0">
          <img src="{{ $user->avatar_url }}" alt="Foto profil kamu" data-preview class="h-full w-full object-cover">
        </div>
        <label class="cursor-pointer">
          <div class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            Ganti Foto
          </div>
          <input type="file" name="avatar" accept="image/*" class="sr-only" aria-label="Upload foto profil baru">
        </label>
      </div>
      @error('avatar')<p class="mt-2 text-xs text-red-500" role="alert">{{ $message }}</p>@enderror
    </div>

    <hr class="border-gray-100">

    {{-- Email --}}
    <div>
      <label for="email" class="mb-1.5 block text-sm font-bold text-gray-700">Email <span class="text-red-500" aria-hidden="true">*</span></label>
      <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
             class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:outline-none focus:ring-0 transition"
             aria-required="true">
      @error('email')<p class="mt-1 text-xs text-red-500" role="alert">{{ $message }}</p>@enderror
    </div>

    {{-- Username --}}
    <div>
      <label for="username" class="mb-1.5 block text-sm font-bold text-gray-700">Username</label>
      <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}"
             class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:outline-none focus:ring-0 transition">
      @error('username')<p class="mt-1 text-xs text-red-500" role="alert">{{ $message }}</p>@enderror
    </div>

    {{-- New password --}}
    <div>
      <label for="password" class="mb-1.5 block text-sm font-bold text-gray-700">Password Baru</label>
      <input id="password" type="password" name="password"
             placeholder="Kosongkan jika tidak ingin mengubah password"
             minlength="6"
             class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:outline-none focus:ring-0 transition">
      @error('password')<p class="mt-1 text-xs text-red-500" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="flex gap-3 pt-2">
      <a href="{{ route('profile.show') }}" class="flex-1 rounded-xl border border-gray-300 py-2.5 text-center text-sm font-bold text-gray-700 hover:bg-gray-50 transition">
        Batal
      </a>
      <button type="submit" class="flex-1 rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition focus:outline-none focus:ring-2 focus:ring-brand-400">
        Simpan Perubahan
      </button>
    </div>
  </form>
</main>
</x-layouts.app>
