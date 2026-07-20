<x-layouts.admin :title="isset($lecturer) ? 'Edit Dosen' : 'Tambah Dosen'" section="dosen">

<div class="page-header">
  <div class="flex items-center gap-3">
    <a href="{{ route('admin.dosen.index') }}"
       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition"
       aria-label="Kembali">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
      </svg>
    </a>
    <div>
      <h1 class="page-title">{{ isset($lecturer) ? 'Edit Dosen' : 'Tambah Dosen Baru' }}</h1>
      <p class="page-subtitle">{{ isset($lecturer) ? 'Perbarui data akun dan profil dosen.' : 'Buat akun user + profil dosen sekaligus.' }}</p>
    </div>
  </div>
</div>

@if($errors->any())
<div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3" role="alert">
  <ul class="text-sm text-red-600 space-y-0.5">
    @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
  </ul>
</div>
@endif

<form action="{{ isset($lecturer) ? route('admin.dosen.update', $lecturer) : route('admin.dosen.store') }}"
      method="POST" enctype="multipart/form-data" data-validate>
@csrf
@if(isset($lecturer)) @method('PUT') @endif

<div class="grid gap-5 lg:grid-cols-[1fr_280px]">

  <div class="space-y-4">

    {{-- Photo --}}
    <div class="card">
      <div class="card-header">Foto Profil</div>
      <div class="card-body flex items-center gap-5">
        <div class="h-16 w-16 overflow-hidden rounded-full bg-gray-100 flex-shrink-0">
          @if(isset($lecturer) && $lecturer->photo)
            <img src="{{ $lecturer->photo && str_starts_with($lecturer->photo,'http') ? $lecturer->photo : Storage::url($lecturer->photo) }}"
                 alt="Foto dosen" data-preview class="h-full w-full object-cover">
          @else
            <img src="https://ui-avatars.com/api/?name=Dosen&background=336cbc&color=fff&size=64"
                 alt="" data-preview class="h-full w-full object-cover">
          @endif
        </div>
        <label class="cursor-pointer btn btn-ghost btn-sm">
          Upload Foto
          <input type="file" name="photo" accept="image/*" class="sr-only" aria-label="Upload foto profil dosen">
        </label>
        @error('photo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Account data --}}
    <div class="card">
      <div class="card-header">Data Akun (Login)</div>
      <div class="card-body space-y-4">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label for="dosen-name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
            <input id="dosen-name" type="text" name="name" required
                   value="{{ old('name', $lecturer->user->name ?? '') }}"
                   placeholder="Dr. Nama Lengkap, M.Kom"
                   class="form-input">
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label for="dosen-email" class="form-label">Email <span class="text-red-500">*</span></label>
            <input id="dosen-email" type="email" name="email" required
                   value="{{ old('email', $lecturer->user->email ?? '') }}"
                   placeholder="email@gmail.com"
                   class="form-input">
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
        </div>

        @if(!isset($lecturer))
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label for="dosen-pass" class="form-label">Password <span class="text-red-500">*</span></label>
            <input id="dosen-pass" type="password" name="password" required minlength="6"
                   placeholder="Minimal 6 karakter" class="form-input">
            @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
          </div>
          <div>
            <label for="dosen-pass2" class="form-label">Konfirmasi Password <span class="text-red-500">*</span></label>
            <input id="dosen-pass2" type="password" name="password_confirmation" required
                   placeholder="Ulangi password" class="form-input">
          </div>
        </div>
        @endif
      </div>
    </div>

    {{-- Lecturer profile data --}}
    <div class="card">
      <div class="card-header">Data Profil Dosen</div>
      <div class="card-body grid gap-4 sm:grid-cols-2">
        <div>
          <label for="dosen-nidn" class="form-label">NIDN</label>
          <input id="dosen-nidn" type="text" name="nidn"
                 value="{{ old('nidn', $lecturer->nidn ?? '') }}"
                 placeholder="0123456789" class="form-input">
          @error('nidn')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label for="dosen-nip" class="form-label">NIP (opsional)</label>
          <input id="dosen-nip" type="text" name="nip"
                 value="{{ old('nip', $lecturer->nip ?? '') }}"
                 placeholder="19860101202012001" class="form-input">
        </div>
        <div class="sm:col-span-2">
          <label for="dosen-address" class="form-label">Alamat</label>
          <input id="dosen-address" type="text" name="address"
                 value="{{ old('address', $lecturer->address ?? '') }}"
                 placeholder="Jl. Contoh No.1, Jakarta" class="form-input">
          @error('address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <div class="card">
      <div class="card-header">Simpan</div>
      <div class="card-body space-y-3">
        <p class="text-xs text-gray-500 leading-relaxed">
          Akun dosen akan dibuat dengan role <strong>Dosen</strong> dan status <strong>Active</strong>.
          Dosen dapat login dengan email + password yang diset di sini.
        </p>
        <button type="submit" class="btn btn-primary w-full justify-center">
          {{ isset($lecturer) ? 'Simpan Perubahan' : '+ Tambah Dosen' }}
        </button>
        <a href="{{ route('admin.dosen.index') }}" class="btn btn-ghost w-full justify-center">
          Batal
        </a>
      </div>
    </div>

    @if(isset($lecturer))
    <div class="card">
      <div class="card-header">Info</div>
      <div class="card-body">
        <dl class="space-y-2 text-xs">
          <div class="flex justify-between"><dt class="text-gray-500">Status</dt>
            <dd><span class="rounded-full px-2 py-0.5 font-semibold {{ $lecturer->status === 'active' ? 'status-active' : 'status-pending' }}">{{ ucfirst($lecturer->status) }}</span></dd>
          </div>
          <div class="flex justify-between"><dt class="text-gray-500">Terdaftar</dt>
            <dd class="font-semibold text-gray-800">{{ $lecturer->created_at->translatedFormat('j M Y') }}</dd>
          </div>
        </dl>
      </div>
    </div>
    @endif
  </div>

</div>
</form>

</x-layouts.admin>
