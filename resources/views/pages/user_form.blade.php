<x-layouts.admin title="Form User — SI-Pedia" section="users">
<main class="mx-auto max-w-[680px] px-8 py-10">
  <div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-700 transition text-xl">←</a>
    <div>
      <h1 class="page-title">{{ $mode === 'create' ? 'Tambah User Baru' : 'Edit User' }}</h1>
      <p class="text-sm text-gray-500 mt-0.5">{{ $mode === 'create' ? 'Buat akun user baru di sistem.' : 'Perbarui informasi akun user.' }}</p>
    </div>
  </div>

  @if($errors->any())
    <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3">
      <ul class="text-sm text-red-600 space-y-1">
        @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form action="{{ $mode === 'create' ? route('admin.users.store') : route('admin.users.update', $user) }}"
        method="POST" class="space-y-5 rounded-2xl border border-gray-200 bg-white p-8 shadow-sm">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div>
      <label class="block text-sm font-bold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
      <input type="text" name="name" value="{{ old('name', $user->name) }}" required
             class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
    </div>

    <div>
      <label class="block text-sm font-bold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
      <input type="email" name="email" value="{{ old('email', $user->email) }}" required
             class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
    </div>

    <div>
      <label class="block text-sm font-bold text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
      <select name="role" required class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
        <option value="user"  @selected(old('role', $user->role) === 'user')>User (Mahasiswa)</option>
        <option value="dosen" @selected(old('role', $user->role) === 'dosen')>Dosen</option>
        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
      </select>
    </div>

    {{-- Field tambahan jika role dosen --}}
    <div id="dosen-fields" class="{{ old('role', $user->role) === 'dosen' ? '' : 'hidden' }} space-y-4 rounded-xl border border-blue-100 bg-blue-50 p-4">
      <p class="text-xs font-bold text-blue-700">Data Profil Dosen (opsional, bisa dilengkapi nanti)</p>
      <div>
        <label class="block text-sm font-bold text-gray-700 mb-1.5">NIDN</label>
        <input type="text" name="nidn" value="{{ old('nidn', $user->lecturer->nidn ?? '') }}"
               class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-brand-600 focus:ring-0">
      </div>
      <div>
        <label class="block text-sm font-bold text-gray-700 mb-1.5">Alamat</label>
        <input type="text" name="address" value="{{ old('address', $user->lecturer->address ?? '') }}"
               class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-brand-600 focus:ring-0">
      </div>
    </div>

    <div>
      <label class="block text-sm font-bold text-gray-700 mb-1.5">
        Password {{ $mode === 'edit' ? '(kosongkan jika tidak ingin diubah)' : '' }}
        @if($mode === 'create') <span class="text-red-500">*</span> @endif
      </label>
      <input type="password" name="password" @if($mode === 'create') required @endif
             placeholder="{{ $mode === 'edit' ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}"
             class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
    </div>

    <div>
      <label class="block text-sm font-bold text-gray-700 mb-1.5">Konfirmasi Password @if($mode === 'create') <span class="text-red-500">*</span> @endif</label>
      <input type="password" name="password_confirmation" @if($mode === 'create') required @endif
             placeholder="Ulangi password"
             class="w-full rounded-xl border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-800 focus:border-brand-600 focus:ring-0">
    </div>

    <div class="flex gap-3 pt-2">
      <a href="{{ route('admin.users.index') }}"
         class="flex-1 rounded-xl border border-gray-300 py-2.5 text-sm font-bold text-gray-700 text-center hover:bg-gray-50 transition">
        Batal
      </a>
      <button type="submit"
              class="flex-1 rounded-xl bg-brand-600 py-2.5 text-sm font-bold text-white hover:bg-brand-700 transition">
        {{ $mode === 'create' ? '+ Buat User' : 'Simpan Perubahan' }}
      </button>
    </div>
  </form>
</main>

<script>
document.querySelector('select[name=role]').addEventListener('change', function() {
    document.getElementById('dosen-fields').classList.toggle('hidden', this.value !== 'dosen');
});
</script>
</x-layouts.admin>
