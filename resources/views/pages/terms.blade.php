<x-layouts.app title="Terms of Service — SI-Pedia" active="Terms" footer="full">

<section class="bg-ink-900 py-12 sm:py-16">
  <div class="mx-auto max-w-[1100px] px-4 sm:px-8 text-center">
    <span class="inline-block rounded-full bg-brand-600/15 px-4 py-1.5 text-sm font-semibold text-brand-400 mb-4">KETENTUAN LAYANAN</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Terms of Service</h1>
    <p class="mt-4 text-base sm:text-lg text-white/60 max-w-2xl mx-auto">Terakhir diperbarui: {{ now()->translatedFormat('j F Y') }}</p>
  </div>
</section>

<section class="bg-white py-12 sm:py-16">
  <div class="mx-auto max-w-[820px] px-4 sm:px-8 space-y-8 text-sm leading-relaxed text-gray-600">
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">1. Penerimaan Ketentuan</h2>
      <p class="text-justify">Dengan mengakses dan menggunakan SI-Pedia, Anda menyetujui untuk terikat oleh ketentuan layanan ini. Jika Anda tidak setuju dengan ketentuan ini, mohon untuk tidak menggunakan platform kami.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">2. Penggunaan Akun</h2>
      <p class="text-justify">Pengguna bertanggung jawab menjaga kerahasiaan kredensial akun mereka. Setiap aktivitas yang terjadi pada akun menjadi tanggung jawab pemilik akun. SI-Pedia berhak menangguhkan akun yang melanggar ketentuan.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">3. Konten Pengguna</h2>
      <p class="text-justify">Artikel dan konten yang diunggah pengguna harus orisinal atau memiliki izin penggunaan yang sah, tidak melanggar hukum, dan tidak mengandung unsur SARA, pornografi, atau kekerasan. Konten yang melanggar akan ditolak atau dihapus oleh admin.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">4. Hak Kekayaan Intelektual</h2>
      <p class="text-justify">Hak cipta atas konten yang dipublikasikan tetap menjadi milik penulis masing-masing. Dengan mempublikasikan artikel di SI-Pedia, penulis memberikan izin kepada platform untuk menampilkan dan mendistribusikan konten tersebut secara publik.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">5. Moderasi & Penghapusan</h2>
      <p class="text-justify">Admin berhak meninjau, menyetujui, menolak, atau menghapus konten apa pun yang dianggap melanggar ketentuan layanan tanpa pemberitahuan sebelumnya.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">6. Perubahan Ketentuan</h2>
      <p class="text-justify">SI-Pedia dapat memperbarui ketentuan layanan ini dari waktu ke waktu. Perubahan akan diinformasikan melalui halaman ini.</p>
    </div>
    <div>
      <h2 class="mb-2 text-base font-bold text-gray-900">7. Kontak</h2>
      <p class="text-justify">Pertanyaan mengenai ketentuan layanan ini dapat disampaikan melalui halaman <a href="{{ route('contact') }}" class="font-semibold text-brand-700 hover:underline">Contact Us</a>.</p>
    </div>
  </div>
</section>

</x-layouts.app>
