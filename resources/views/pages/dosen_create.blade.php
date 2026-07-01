<x-layouts.admin title="Form Dosen — SI-Pedia" section="dosen">
<main class="mx-auto max-w-[1100px] px-8 py-10">
  <div class="flex items-center gap-4">
    <a href="{{ route('admin.dosen.index') }}" class="text-4xl hover:text-brand-600 transition-colors">←</a>
    <h1 class="text-5xl font-extrabold">{{ isset($lecturer) ? 'Edit Dosen' : 'Tambah Dosen' }}</h1>
  </div>
  <p class="ml-14 mt-2 text-2xl text-gray-700">
    {{ isset($lecturer) ? 'Perbarui data dosen.' : 'Buat akun dan profil dosen sekaligus.' }}
  </p>

  <form action="{{ isset($lecturer) ? route('admin.dosen.update', $lecturer) : route('admin.dosen.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($lecturer)) @method('PUT') @endif

    {{-- FOTO PROFIL --}}
    <div class="mt-8 rounded-3xl border border-gray-200 p-12 shadow-sm bg-white">
      <h2 class="text-3xl font-extrabold mb-5">Foto Profil</h2>
      <div class="flex items-end gap-8">
        @if(isset($lecturer) && $lecturer->photo)
            <img src="{{ $lecturer->photo ? (str_starts_with($lecturer->photo, "http") ? $lecturer->photo : Storage::url($lecturer->photo)) : null }}" class="h-40 w-40 rounded-full object-cover shadow-sm">
        @else
            <div class="grid h-40 w-40 place-items-center rounded-full bg-gray-200 text-5xl">📷</div>
        @endif
        <label class="cursor-pointer rounded-xl bg-brand-600 px-10 py-4 text-2xl font-bold text-white shadow hover:bg-brand-700 transition">
            Pilih Foto
            <input type="file" name="photo" accept="image/*" class="hidden">
        </label>
      </div>
      @error('photo') <span class="text-red-500 font-bold mt-2 block">{{ $message }}</span> @enderror
    </div>

    {{-- DATA AKUN (users table) --}}
    <div class="mt-6 rounded-3xl border border-gray-200 p-12 shadow-sm bg-white">
      <h2 class="text-3xl font-extrabold mb-8">Data Akun</h2>
      <div class="space-y-10">

        <div>
          <label class="mb-3 block text-3xl font-extrabold">Nama Lengkap</label>
          <div class="flex items-center gap-5 rounded-2xl border-2 focus-within:border-brand-600 border-gray-300 px-7 py-4 bg-white transition">
            <span class="text-3xl text-gray-400">👤</span>
            <input type="text" name="name" value="{{ old('name', $lecturer->user->name ?? '') }}"
              required placeholder="Masukkan nama lengkap"
              class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0">
          </div>
          @error('name') <span class="text-red-500 font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="mb-3 block text-3xl font-extrabold">Email</label>
          <div class="flex items-center gap-5 rounded-2xl border-2 focus-within:border-brand-600 border-gray-300 px-7 py-4 bg-white transition">
            <span class="text-3xl text-gray-400">✉️</span>
            <input type="email" name="email" value="{{ old('email', $lecturer->user->email ?? '') }}"
              required placeholder="Masukkan email dosen"
              class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0">
          </div>
          @error('email') <span class="text-red-500 font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>

        @if(!isset($lecturer))
        <div>
          <label class="mb-3 block text-3xl font-extrabold">Password</label>
          <div class="flex items-center gap-5 rounded-2xl border-2 focus-within:border-brand-600 border-gray-300 px-7 py-4 bg-white transition">
            <span class="text-3xl text-gray-400">🔒</span>
            <input type="password" name="password" required placeholder="Minimal 6 karakter"
              class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0">
          </div>
          @error('password') <span class="text-red-500 font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="mb-3 block text-3xl font-extrabold">Konfirmasi Password</label>
          <div class="flex items-center gap-5 rounded-2xl border-2 focus-within:border-brand-600 border-gray-300 px-7 py-4 bg-white transition">
            <span class="text-3xl text-gray-400">🔒</span>
            <input type="password" name="password_confirmation" required placeholder="Ulangi password"
              class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0">
          </div>
        </div>
        @endif

      </div>
    </div>

    {{-- DATA PROFIL DOSEN (lecturers table) --}}
    <div class="mt-6 rounded-3xl border border-gray-200 p-12 shadow-sm bg-white">
      <h2 class="text-3xl font-extrabold mb-8">Data Profil Dosen</h2>
      <div class="space-y-10">

        <div>
          <label class="mb-3 block text-3xl font-extrabold">NIDN</label>
          <div class="flex items-center gap-5 rounded-2xl border-2 focus-within:border-brand-600 border-gray-300 px-7 py-4 bg-white transition">
            <span class="text-3xl text-gray-400">⠿</span>
            <input type="text" name="nidn" value="{{ old('nidn', $lecturer->nidn ?? '') }}"
              required placeholder="Masukkan NIDN"
              class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0">
          </div>
          @error('nidn') <span class="text-red-500 font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>

        <div>
          <label class="mb-3 block text-3xl font-extrabold">Alamat</label>
          <div class="flex items-center gap-5 rounded-2xl border-2 focus-within:border-brand-600 border-gray-300 px-7 py-4 bg-white transition">
            <span class="text-3xl text-gray-400">📍</span>
            <input type="text" name="address" value="{{ old('address', $lecturer->address ?? '') }}"
              required placeholder="Masukkan alamat lengkap"
              class="w-full text-2xl font-bold text-gray-800 bg-transparent border-none focus:ring-0 p-0">
          </div>
          @error('address') <span class="text-red-500 font-bold mt-2 block">{{ $message }}</span> @enderror
        </div>

      </div>
    </div>

    <div class="mt-8 flex items-center justify-between">
      <a href="{{ route('admin.dosen.index') }}" class="rounded-2xl bg-gray-200 px-16 py-5 text-2xl font-bold text-gray-700 hover:bg-gray-300 transition">Batal</a>
      <button type="submit" class="rounded-2xl bg-brand-600 px-14 py-5 text-2xl font-bold text-white shadow hover:bg-brand-700 transition">
        {{ isset($lecturer) ? 'Simpan Perubahan' : 'Tambah Dosen' }}
      </button>
    </div>
  </form>
</main>
</x-layouts.admin>
