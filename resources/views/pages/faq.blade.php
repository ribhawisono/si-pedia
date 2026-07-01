<x-layouts.app title="FAQ — SI-Pedia" active="FAQ" footer="full">

<section class="bg-ink-900 py-16">
  <div class="mx-auto max-w-[1100px] px-8 text-center">
    <span class="inline-block rounded-full bg-brand-600/15 px-4 py-1.5 text-sm font-semibold text-brand-400 mb-4">PUSAT BANTUAN</span>
    <h1 class="text-4xl font-black text-white sm:text-5xl">Frequently Asked Questions</h1>
    <p class="mt-4 text-lg text-white/60 max-w-2xl mx-auto">
      Temukan jawaban atas pertanyaan yang sering ditanyakan seputar SI-Pedia.
    </p>
  </div>
</section>

<section class="bg-white py-16">
  <div class="mx-auto max-w-[820px] px-8">

    @php
    $faqs = [
      [
        'category' => 'Umum',
        'icon' => '📘',
        'items' => [
          ['q' => 'Apa itu SI-Pedia?',
           'a' => 'SI-Pedia adalah platform ensiklopedia digital resmi Program Studi Sistem Informasi Universitas Indraprasta PGRI. Platform ini menyediakan artikel, informasi akademik, profil dosen, dan sumber daya relevan untuk sivitas akademika prodi SI.'],
          ['q' => 'Siapa yang bisa mengakses SI-Pedia?',
           'a' => 'Semua orang bisa membaca artikel dan konten publik di SI-Pedia tanpa perlu login. Untuk menulis artikel atau menggunakan fitur komunitas, kamu perlu mendaftar akun terlebih dahulu.'],
          ['q' => 'Apakah SI-Pedia resmi dari kampus?',
           'a' => 'Ya, SI-Pedia adalah platform resmi yang dikelola oleh Program Studi Sistem Informasi Universitas Indraprasta PGRI.'],
        ],
      ],
      [
        'category' => 'Akun & Registrasi',
        'icon' => '👤',
        'items' => [
          ['q' => 'Bagaimana cara mendaftar akun?',
           'a' => 'Klik tombol Register di pojok kanan atas halaman, isi nama, email, dan password. Setelah mendaftar, kamu akan menerima email verifikasi — klik link di email tersebut untuk mengaktifkan akun.'],
          ['q' => 'Apa perbedaan role User, Dosen, dan Admin?',
           'a' => 'User (mahasiswa) dapat membaca dan menulis artikel yang perlu persetujuan admin. Dosen memiliki akses yang sama namun terhubung ke data profil dosen di sistem. Admin memiliki kontrol penuh atas seluruh konten, persetujuan artikel, dan manajemen pengguna.'],
          ['q' => 'Saya lupa password, bagaimana cara reset?',
           'a' => 'Klik "Lupa Password" di halaman login, masukkan email yang terdaftar, dan ikuti instruksi di email yang dikirimkan untuk membuat password baru.'],
          ['q' => 'Bagaimana cara menjadi Dosen di SI-Pedia?',
           'a' => 'Akun Dosen dibuat oleh Admin. Jika kamu adalah dosen di Prodi SI, hubungi administrator untuk didaftarkan. Admin akan membuat akun sekaligus profil dosenmu di sistem.'],
        ],
      ],
      [
        'category' => 'Artikel & Konten',
        'icon' => '✏️',
        'items' => [
          ['q' => 'Bagaimana cara menulis artikel?',
           'a' => 'Setelah login, klik tombol "Tulis Artikel" di navbar. Isi judul, pilih kategori, tambahkan thumbnail, dan tulis konten artikel. Kamu bisa simpan sebagai Draft atau langsung Submit ke admin untuk direview.'],
          ['q' => 'Kenapa artikel saya belum muncul di publik?',
           'a' => 'Artikel yang ditulis oleh mahasiswa dan dosen perlu melalui proses review oleh Admin terlebih dahulu sebelum dipublikasikan. Status artikel bisa dicek di halaman "Artikel Saya".'],
          ['q' => 'Apa arti status-status artikel?',
           'a' => "Draft: artikel tersimpan, hanya kamu yang bisa lihat. Pending: artikel sudah disubmit, menunggu persetujuan admin. Active: artikel disetujui dan tampil ke publik. Pending Delete: kamu sudah meminta artikel dihapus dan admin sedang mempertimbangkan."],
          ['q' => 'Bagaimana cara menghapus artikel yang sudah saya buat?',
           'a' => 'Di halaman "Artikel Saya", klik tombol "Request Hapus" pada artikel yang ingin dihapus. Admin akan menerima notifikasi dan memutuskan apakah akan menghapus artikel tersebut.'],
        ],
      ],
      [
        'category' => 'Keamanan & Pelaporan',
        'icon' => '🛡️',
        'items' => [
          ['q' => 'Bagaimana cara melaporkan konten atau akun yang melanggar?',
           'a' => 'Untuk melaporkan akun: buka profil pengguna dan klik tombol "Laporkan Akun". Untuk melaporkan artikel: gunakan fitur komentar atau hubungi admin langsung. Setiap laporan akan ditinjau oleh admin.'],
          ['q' => 'Apa yang terjadi setelah saya melaporkan sebuah akun?',
           'a' => 'Laporanmu akan masuk ke panel admin dengan status "Pending". Admin akan meninjau laporan dan mengambil tindakan yang diperlukan, lalu status laporan akan diperbarui menjadi "Reviewed" atau "Dismissed".'],
          ['q' => 'Apakah data saya aman di SI-Pedia?',
           'a' => 'Kami menjaga keamanan data pengguna dengan enkripsi password dan tidak membagikan data pribadi kepada pihak ketiga. Hanya admin yang dapat melihat informasi akun pengguna di sistem.'],
        ],
      ],
    ];
    @endphp

    @foreach($faqs as $section)
    <div class="mb-12">
      <div class="flex items-center gap-3 mb-6">
        <span class="text-2xl">{{ $section['icon'] }}</span>
        <h2 class="text-2xl font-extrabold text-gray-900">{{ $section['category'] }}</h2>
      </div>
      <div class="space-y-3">
        @foreach($section['items'] as $index => $faq)
        <details class="group rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
          <summary class="flex cursor-pointer items-center justify-between px-6 py-4 font-bold text-gray-900 hover:bg-gray-50 transition-colors list-none">
            <span>{{ $faq['q'] }}</span>
            <svg class="h-5 w-5 flex-shrink-0 text-gray-400 transition-transform group-open:rotate-180"
                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </summary>
          <div class="border-t border-gray-100 px-6 py-4 text-sm leading-relaxed text-gray-600">
            {{ $faq['a'] }}
          </div>
        </details>
        @endforeach
      </div>
    </div>
    @endforeach

    {{-- CTA --}}
    <div class="mt-8 rounded-2xl bg-ink-900 p-8 text-center">
      <p class="text-lg font-bold text-white">Masih punya pertanyaan lain?</p>
      <p class="mt-2 text-sm text-white/60">Hubungi admin atau kunjungi halaman About untuk informasi lebih lanjut.</p>
      <div class="mt-5 flex justify-center gap-4">
        <a href="{{ route('about') }}" class="rounded-lg border border-white/20 px-6 py-2.5 text-sm font-semibold text-white hover:bg-white/10 transition">
          Tentang Kami
        </a>
        @auth
        <a href="{{ route('profile.show') }}" class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
          Ke Profil Saya
        </a>
        @else
        <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
          Daftar Sekarang
        </a>
        @endauth
      </div>
    </div>

  </div>
</section>

</x-layouts.app>
