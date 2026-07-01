<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Lecturer;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Bersihkan data lama (urutan FK-safe) ─────────────────────────────
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('lecturers')->truncate();
        DB::table('articles')->truncate();
        DB::table('reviews')->truncate();
        DB::table('users')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── Admin ─────────────────────────────────────────────────────────────
        $admin = User::create([
            'name'              => 'Admin SI-Pedia',
            'username'          => 'admin',
            'email'             => 'adminSIPedia@gmail.com',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // ── User biasa (mahasiswa) ─────────────────────────────────────────────
        User::create([
            'name'              => 'Ucup Pratama',
            'username'          => 'ucup',
            'email'             => 'ucupganteng@gmail.com',
            'password'          => Hash::make('password'),
            'role'              => 'user',
            'study_program'     => 'Sistem Informasi',
            'force'             => '2024',
            'email_verified_at' => now(),
        ]);

        // ── Dosen (User + Lecturer record) ────────────────────────────────────
        $dosens = [
            [
                'name'    => 'Dr. Budi Santoso, M.Kom',
                'email'   => 'budisantoso.si@gmail.com',
                'nidn'    => '0412017601',
                'address' => 'Jl. Kebagusan Raya No.12, Jakarta Selatan',
                'photo'   => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=336cbc&color=fff&size=200',
            ],
            [
                'name'    => 'Siti Rahmawati, M.T',
                'email'   => 'sitirahmawati.si@gmail.com',
                'nidn'    => '0523058502',
                'address' => 'Jl. Siliwangi No.45, Bogor',
                'photo'   => 'https://ui-avatars.com/api/?name=Siti+Rahmawati&background=e91e8c&color=fff&size=200',
            ],
            [
                'name'    => 'Andi Pratama, S.Kom, M.T',
                'email'   => 'andipratama.si@gmail.com',
                'nidn'    => '0318099003',
                'address' => 'Jl. Margonda Raya No.8, Depok',
                'photo'   => 'https://ui-avatars.com/api/?name=Andi+Pratama&background=1e88e5&color=fff&size=200',
            ],
            [
                'name'    => 'Dr. Reza Fauzi, M.Kom',
                'email'   => 'rezafauzi.si@gmail.com',
                'nidn'    => '0701078803',
                'address' => 'Jl. Ahmad Yani No.77, Bekasi',
                'photo'   => 'https://ui-avatars.com/api/?name=Reza+Fauzi&background=43a047&color=fff&size=200',
            ],
            [
                'name'    => 'Irwan Hidayat, M.Kom',
                'email'   => 'irwanhidayat.si@gmail.com',
                'nidn'    => '0915018504',
                'address' => 'Jl. Raya Cibinong No.23, Bogor',
                'photo'   => 'https://ui-avatars.com/api/?name=Irwan+Hidayat&background=f57c00&color=fff&size=200',
            ],
        ];

        foreach ($dosens as $d) {
            $user = User::create([
                'name'              => $d['name'],
                'email'             => $d['email'],
                'password'          => Hash::make('password'),
                'role'              => 'dosen',
                'email_verified_at' => now(),
            ]);
            Lecturer::create([
                'user_id' => $user->id,
                'nidn'    => $d['nidn'],
                'address' => $d['address'],
                'photo'   => $d['photo'],
                'status'  => 'active',
            ]);
        }

        // ── Kategori ──────────────────────────────────────────────────────────
        $cats = collect(['Berita', 'Event', 'Akademik', 'Lomba'])
            ->mapWithKeys(fn ($n) => [$n => Category::create(['name' => $n])->id]);

        // ── Artikel dengan gambar dari Picsum (stable, permanent URLs) ────────
        $articles = [
            [
                'title'   => 'Penerimaan Mahasiswa Baru (PMB) Universitas Indraprasta PGRI 2026/2027 Resmi Dibuka!',
                'cat'     => 'Berita',
                'date'    => '2026-04-13',
                'views'   => 240,
                'image'   => 'https://picsum.photos/seed/pmb-unindra/800/450',
                'content' => 'Universitas Indraprasta PGRI (Unindra) kembali membuka Penerimaan Mahasiswa Baru (PMB) untuk tahun akademik 2026/2027. Pendaftaran dapat dilakukan secara online melalui website resmi Unindra. Program Studi Sistem Informasi menjadi salah satu program studi unggulan yang banyak diminati calon mahasiswa baru karena relevansinya dengan kebutuhan industri digital saat ini.

Calon mahasiswa baru yang mendaftar akan mendapatkan berbagai keuntungan, termasuk beasiswa prestasi, program mentoring dengan dosen berpengalaman, dan akses ke laboratorium komputer modern. Unindra berkomitmen untuk menghasilkan lulusan yang kompeten dan siap bersaing di era digital.

Untuk informasi lebih lanjut mengenai persyaratan, jadwal pendaftaran, dan program beasiswa, calon mahasiswa dapat mengunjungi website resmi Unindra atau menghubungi bagian admisi.',
            ],
            [
                'title'   => 'Selamat Hari Lahir Pancasila — Unindra Gelar Upacara Bendera',
                'cat'     => 'Event',
                'date'    => '2026-06-01',
                'views'   => 190,
                'image'   => 'https://picsum.photos/seed/pancasila-day/800/450',
                'content' => 'Rektor dan segenap Civitas Academica Universitas Indraprasta PGRI mengucapkan Selamat Hari Lahir Pancasila yang diperingati setiap tanggal 1 Juni. Dalam rangka memperingati Hari Lahir Pancasila, Unindra menggelar upacara bendera yang diikuti oleh seluruh civitas akademika.

Upacara berlangsung khidmat dengan diawali pengibaran bendera Merah Putih, pembacaan teks Pancasila, dan sambutan dari Rektor yang menekankan pentingnya nilai-nilai Pancasila dalam kehidupan sehari-hari, khususnya di lingkungan kampus.

Program Studi Sistem Informasi turut berpartisipasi aktif dalam kegiatan ini dengan menampilkan pameran teknologi yang mengangkat tema "Teknologi untuk Indonesia yang Lebih Baik", mencerminkan semangat Pancasila dalam konteks digital modern.',
            ],
            [
                'title'   => 'Program Course Artificial Intelligence (AI) untuk Mahasiswa SI',
                'cat'     => 'Akademik',
                'date'    => '2026-01-24',
                'views'   => 155,
                'image'   => 'https://picsum.photos/seed/artificial-intelligence/800/450',
                'content' => 'Program Studi Sistem Informasi Unindra meluncurkan program kursus intensif Artificial Intelligence (AI) yang dirancang khusus untuk mahasiswa semester 3 ke atas. Program ini mencakup materi Machine Learning, Deep Learning, Natural Language Processing, dan Computer Vision.

Kursus ini dirancang untuk membekali mahasiswa dengan keterampilan praktis dalam mengimplementasikan AI di berbagai bidang industri. Peserta akan mengerjakan proyek nyata yang dapat dijadikan portofolio saat melamar pekerjaan.

Program berlangsung selama 3 bulan dengan sesi tatap muka setiap akhir pekan dan mentoring online sepanjang minggu. Daftar sekarang melalui portal akademik Unindra sebelum kuota terpenuhi.',
            ],
            [
                'title'   => 'Pengumuman: Sosialisasi Satria Data & Lomba Inovasi Digital Mahasiswa 2026',
                'cat'     => 'Lomba',
                'date'    => '2026-05-22',
                'views'   => 130,
                'image'   => 'https://picsum.photos/seed/lomba-inovasi/800/450',
                'content' => 'Pusat Prestasi Nasional Kemdikbud kembali menyelenggarakan Satria Data 2026, kompetisi data science dan kecerdasan buatan tingkat nasional untuk mahasiswa. Program Studi Sistem Informasi Unindra mengundang seluruh mahasiswa untuk berpartisipasi dalam kompetisi bergengsi ini.

Satria Data 2026 terdiri dari beberapa kategori: Data Visualization, Machine Learning Challenge, dan AI Innovation. Total hadiah senilai Rp 500 juta menanti para pemenang di setiap kategori.

Pendaftaran tim dibuka mulai 1 Juni 2026 dan dapat dilakukan melalui portal Satria Data. Setiap tim terdiri dari 2-3 mahasiswa. Prodi SI Unindra siap memberikan pembinaan dan bimbingan teknis bagi tim yang terpilih mewakili kampus.',
            ],
            [
                'title'   => 'Workshop Business Intelligence: Dari Data ke Keputusan Bisnis',
                'cat'     => 'Akademik',
                'date'    => '2026-03-10',
                'views'   => 88,
                'image'   => 'https://picsum.photos/seed/business-intelligence/800/450',
                'content' => 'Prodi Sistem Informasi Unindra menyelenggarakan Workshop Business Intelligence yang menghadirkan praktisi industri dari berbagai perusahaan ternama. Workshop ini bertujuan untuk memberikan pemahaman mendalam tentang penerapan BI dalam pengambilan keputusan bisnis.

Materi workshop mencakup pengenalan tools BI seperti Tableau, Power BI, dan Google Data Studio, serta studi kasus nyata dari perusahaan-perusahaan di Indonesia. Peserta juga berkesempatan untuk berinteraksi langsung dengan para praktisi dan menggali pengalaman kerja di bidang BI.

Workshop terbuka untuk mahasiswa semester 5 ke atas dan alumni Prodi SI Unindra. Pendaftaran gratis namun tempat terbatas. Segera daftarkan diri melalui tautan di bio Instagram @prodi.si.unindra.',
            ],
            [
                'title'   => 'Mahasiswa SI Unindra Raih Juara 1 di Kompetisi Nasional App Development',
                'cat'     => 'Berita',
                'date'    => '2026-02-28',
                'views'   => 312,
                'image'   => 'https://picsum.photos/seed/juara-nasional/800/450',
                'content' => 'Tim mahasiswa Program Studi Sistem Informasi Unindra berhasil meraih Juara 1 dalam kompetisi App Development tingkat nasional yang diselenggarakan oleh Kominfo. Tim yang beranggotakan tiga mahasiswa semester 6 ini mengembangkan aplikasi sistem monitoring lingkungan berbasis IoT dan AI.

Aplikasi yang diberi nama "EnviroSense" ini mampu memantau kualitas udara, suhu, dan kelembaban secara real-time menggunakan sensor IoT yang terhubung ke dashboard berbasis web. Teknologi AI yang terintegrasi dapat memprediksi perubahan kondisi lingkungan dan memberikan rekomendasi tindakan preventif.

Keberhasilan ini merupakan bukti nyata komitmen Prodi SI Unindra dalam menghasilkan lulusan yang inovatif dan mampu bersaing di tingkat nasional. Dekan dan Kaprodi mengucapkan selamat kepada tim yang telah mengharumkan nama Unindra.',
            ],
        ];

        foreach ($articles as $a) {
            Article::create([
                'user_id'    => $admin->id,
                'title'      => $a['title'],
                'slug'       => \Illuminate\Support\Str::slug($a['title']) . '-' . rand(100, 999),
                'category_id'=> $cats[$a['cat']],
                'writer'     => 'Admin SI-Pedia',
                'status'     => 'active',
                'views'      => $a['views'],
                'image'      => $a['image'],
                'content'    => $a['content'],
                'created_at' => $a['date'],
                'updated_at' => $a['date'],
            ]);
        }

        // ── Reviews ───────────────────────────────────────────────────────────
        $reviewData = [
            ['title' => 'Platform yang Sangat Informatif!', 'desc' => 'SI-Pedia sangat membantu saya dalam mencari informasi seputar program studi. Artikelnya lengkap dan mudah dipahami.'],
            ['title' => 'Referensi Akademik Terbaik', 'desc' => 'Sebagai mahasiswa baru, SI-Pedia menjadi referensi utama saya untuk memahami dunia Sistem Informasi. Terima kasih!'],
            ['title' => 'Desain Modern dan User-Friendly', 'desc' => 'Tampilan SI-Pedia sangat modern dan nyaman digunakan. Navigasinya intuitif dan kontennya selalu up-to-date.'],
            ['title' => 'Konten Berkualitas Tinggi', 'desc' => 'Artikel-artikel di SI-Pedia ditulis dengan baik dan berdasarkan sumber yang terpercaya. Sangat bermanfaat untuk penelitian.'],
        ];
        foreach ($reviewData as $r) {
            Review::create([
                'title'       => $r['title'],
                'type'        => 'Social media',
                'description' => $r['desc'],
                'views'       => rand(80, 400),
                'status'      => 'pending',
                'reviewed_at' => '2026-06-01',
                'image'       => 'https://picsum.photos/seed/' . \Illuminate\Support\Str::slug($r['title']) . '/400/200',
            ]);
        }
    }
}
