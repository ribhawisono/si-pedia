<x-layouts.app title="Privacy Policy — SI-Pedia" active="Privacy" footer="full">

<section class="bg-ink-900 py-12 sm:py-16">
  <div class="mx-auto max-w-[1100px] px-4 sm:px-8 text-center">
    <span class="inline-block rounded-full bg-brand-600/15 px-4 py-1.5 text-sm font-semibold text-brand-400 mb-4">KEBIJAKAN PRIVASI</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Privacy Policy</h1>
    <p class="mt-4 text-base sm:text-lg text-white/60 max-w-2xl mx-auto">Terakhir diperbarui: {{ now()->translatedFormat('j F Y') }}</p>
  </div>
</section>

<section class="bg-white py-12 sm:py-16">
  <div class="mx-auto max-w-[820px] px-4 sm:px-8 space-y-8 text-sm leading-relaxed text-gray-600">
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">1. Data yang Kami Kumpulkan</h2>
      <p class="text-justify">SI-Pedia mengumpulkan data yang Anda berikan saat mendaftar, seperti nama, email, dan role (mahasiswa/dosen). Kami juga mencatat aktivitas dasar seperti riwayat baca dan bookmark untuk meningkatkan pengalaman Anda.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">2. Penggunaan Data</h2>
      <p class="text-justify">Data digunakan untuk autentikasi akun, personalisasi konten, moderasi artikel dan komentar, serta komunikasi terkait verifikasi email (OTP) dan notifikasi penting lainnya.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">3. Keamanan Data</h2>
      <p class="text-justify">Password pengguna dienkripsi dan tidak pernah disimpan dalam bentuk teks biasa. Akses ke data akun dibatasi hanya untuk administrator platform.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">4. Berbagi Data</h2>
      <p class="text-justify">Kami tidak membagikan data pribadi Anda kepada pihak ketiga untuk tujuan komersial. Data hanya dapat diakses oleh admin untuk keperluan moderasi dan pengelolaan platform.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">5. Hak Pengguna</h2>
      <p class="text-justify">Anda dapat memperbarui atau menghapus informasi profil melalui halaman Profil. Untuk permintaan penghapusan akun secara penuh, silakan hubungi admin melalui halaman <a href="{{ route('contact') }}" class="font-semibold text-brand-700 hover:underline">Contact Us</a>.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">6. Perubahan Kebijakan</h2>
      <p class="text-justify">Kebijakan privasi ini dapat diperbarui sewaktu-waktu. Perubahan signifikan akan diinformasikan melalui platform.</p>
    </div>
  </div>
</section>

</x-layouts.app>
