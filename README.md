# SI-Pedia

Ensiklopedia digital untuk Program Studi Sistem Informasi, Universitas Indraprasta PGRI. Platform ini memungkinkan pengelolaan artikel, data dosen, kategori, review, dan komentar — dengan panel admin lengkap dan dashboard statistik.

## Fitur

- **Artikel** — CRUD lengkap dengan kategori, gambar, slug otomatis, dan scheduled publishing
- **Alur revisi artikel live** — Mengedit artikel yang sudah publish tidak langsung mengubah yang tayang; tersimpan sebagai usulan (`pending_edit`) dan menunggu approve/reject admin. Riwayat revisi menampilkan before/after tiap perubahan.
- **Takedown artikel** — Admin bisa menurunkan artikel live (beda dari Hapus biasa); penulis tetap bisa memperbaiki & submit ulang dari "Artikel Saya"
- **Preview langsung dari editor** — Tombol Preview menampilkan hasil editan yang belum disimpan (lewat localStorage), bukan menunggu Simpan/Submit dulu. Kalau tidak ada perubahan belum tersimpan, preview menampilkan versi live/tersimpan seperti biasa.
- **Hapus draft mandiri** — Artikel berstatus draft (termasuk hasil reject/takedown) bisa dihapus langsung oleh penulis tanpa approval admin; artikel yang sudah publish tetap wajib lewat persetujuan admin
- **Komentar** — Pengunjung terautentikasi bisa berkomentar di setiap artikel, tayang langsung dengan batas wajar (min 3, maks 150 kata) khas komentar ensiklopedia
- **Filter kata terlarang** — Komentar yang mengandung kata kasar/SARA dari daftar yang dikelola admin otomatis ditahan (pending) untuk ditinjau, bukan langsung tayang
- **Dosen** — Data dosen dengan foto, NIDN, dan proses approval
- **Kategori** — Kelompokkan artikel berdasarkan topik
- **Review** — Sistem review untuk artikel/proyek yang masuk
- **Admin Panel** — Dashboard dengan grafik, statistik, fast action, dan activity log
- **Profil** — Profil user dan admin dengan upload avatar
- **Bulk Action** — Publish, draft, atau hapus artikel secara massal
- **Activity Log** — Jejak aktivitas admin tersimpan otomatis
- **Role-based Access** — Admin dan user dengan akses berbeda
- **Dark mode** — Toggle manual (tersimpan di localStorage), termasuk tampilan artikel (prose) yang kontras
- **REST API v1** — 17 endpoint publik/terautentikasi untuk konsumsi eksternal (lihat bagian [REST API](#rest-api))

## Perubahan Terbaru

Ringkasan perbaikan & fitur yang ditambahkan setelah rilis awal:

**Alur artikel & revisi**
- Edit artikel yang sudah takedown tidak lagi diblokir keliru oleh cek status `active`
- Route admin edit/update artikel kini `withTrashed()` — submit setelah takedown tidak lagi 404
- Restore dari Trash membersihkan `rejection_note` lama supaya tidak muncul catatan basi
- Mengedit artikel yang **sudah live (Active)** kini diperbolehkan, tapi perubahan disimpan sebagai usulan (`article_revisions` berstatus `pending_edit`) — artikel yang tayang baru berubah setelah admin **Setujui** di halaman Revisi; admin juga bisa **Tolak** dengan catatan
- Halaman Riwayat Revisi menampilkan toggle before/after per entri, dan panel Setujui/Tolak usulan (admin) — hanya bisa diakses admin atau penulis artikel sendiri, tidak bocor ke halaman publik
- Layout halaman Preview & Revisi kini dinamis: admin dapat sidebar admin, user biasa dapat layout publik (sebelumnya sidebar admin bocor ke user)
- Route `preview` & `revisions` kini juga tersedia untuk user biasa (sebelumnya hanya admin, menyebabkan link `/admin/...` diakses user → 403/404)
- Tombol Preview di editor kini menampilkan **hasil editan yang belum disimpan** (via localStorage), bukan menunggu draft/publish tersimpan dulu; kalau tidak ada perubahan belum tersimpan, preview otomatis balik ke versi live/tersimpan
- Tombol "Kembali" di halaman Preview tidak lagi diam ketika preview dibuka di tab baru (tanpa browser history) — selalu punya tujuan fallback
- Artikel berstatus **draft** (termasuk hasil ditolak/takedown) bisa dihapus langsung oleh penulis tanpa menunggu approval admin; artikel **Active** tetap wajib lewat Request Hapus → approval admin
- Tombol action di Article Data (admin) sekarang grid 2×2 posisi tetap: Edit/Acc/Batal kiri-atas, Takedown/Tolak kanan-atas, Preview kiri-bawah, Hapus kanan-bawah — konsisten di semua kombinasi status
- Badge "Ada Revisi" muncul di Article Data (admin) untuk artikel active yang punya usulan perubahan menunggu review

**Komentar**
- Komentar kini tayang otomatis (`approved`) seperti komentar biasa, bukan menunggu approval untuk semua orang
- Komentar yang mengandung kata dari daftar **Kata Terlarang** (dikelola admin di halaman Moderasi Komentar, whole-word match case-insensitive) otomatis ditahan `pending` untuk ditinjau — penulis tetap melihat komentarnya sendiri (ditandai "Menunggu moderasi"), pengunjung lain tidak
- Batas wajar jumlah kata per komentar: minimal 3 kata, maksimal 150 kata (terpisah dari filter kata terlarang), dengan counter kata real-time di form
- Tabel baru `banned_words` untuk menyimpan daftar kata terlarang

**Tampilan & dark mode**
- Bold/`<strong>` pada isi artikel sekarang terlihat di dark mode (sebelumnya nyaris hitam-di-atas-gelap) — custom property `--tw-prose-bold` dkk. Tailwind Typography sekarang ikut di-override, bukan cuma warna teks biasa
- Footer & halaman Contact diperbarui ke alamat, telepon, dan email Kampus B yang benar

**Trash & moderasi**
- Badge alasan "Hapus" di halaman Trash sekarang berwarna (merah) alih-alih abu-abu pucat, biar kontras dengan badge "Takedown" (ungu)
- Label di Trash kini menyebut nama penulis asli yang bisa mengedit ("Bisa diedit oleh {nama}") alih-alih teks generik

**Infrastruktur**
- Koneksi PDO MySQL sekarang punya connect timeout eksplisit (`DB_CONNECT_TIMEOUT`, default 5 detik) supaya kegagalan koneksi ke database gagal cepat, bukan menggantung sampai 30 detik lalu FatalError

## Stack

- Laravel 13 · PHP 8.3
- Tailwind CSS v4
- SQLite (default) / MySQL
- Vite 6
- Alpine.js

## Persyaratan

- PHP >= 8.3 dengan ekstensi: `pdo_sqlite`, `mbstring`, `openssl`, `curl`, `gd`, `fileinfo`
- Composer >= 2.x
- Node.js >= 18.x & npm >= 9.x

## Instalasi

```bash
# Clone repo
git clone https://github.com/ribhawisono/si-pedia.git
cd si-pedia

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database sudah disertakan (pre-seeded). Tidak perlu langkah tambahan.
# Jika ingin reset dari awal:
# php artisan migrate:fresh --seed

# Build assets
npm run build

# Jalankan server
php artisan serve
```

Buka `http://localhost:8000` di browser.

Panduan instalasi lengkap (termasuk Windows, macOS, dan deploy ke server): lihat [`tutorial.md`](tutorial.md).

## Akun Default

| Role  | Email                      | Password  |
|-------|----------------------------|-----------|
| Admin | adminSIPedia@gmail.com     | `password` |
| User  | ucupganteng@gmail.com      | `password` |

## Struktur Project

```
app/
├── Http/
│   ├── Controllers/    ← 11 controller (Article, Auth, Category, Comment, Dosen, Homepage, Page, Profile, Review, User)
│   └── Middleware/     ← AdminMiddleware
└── Models/             ← 9 model + HasSlug trait (termasuk ArticleRevision, BannedWord)

database/
├── migrations/         ← migrasi (termasuk article_revisions, banned_words)
└── seeders/            ← DatabaseSeeder (user, kategori, artikel, dosen, review)

resources/
├── css/app.css         ← Tailwind v4 + design tokens + dark mode overrides
├── js/app.js           ← Axios setup, dark mode toggle
└── views/
    ├── auth/           ← 5 halaman autentikasi
    ├── components/     ← 6 komponen reusable
    ├── errors/         ← 3 halaman error (403, 404, 500)
    ├── layouts/        ← Layout utama (admin & publik)
    └── pages/          ← halaman publik + admin (artikel, revisi, preview, trash, moderasi komentar, dll)

routes/
└── web.php             ← Semua routing
```

## Routing

### Publik
| Method | URI | Fungsi |
|--------|-----|--------|
| GET | `/` | Homepage |
| GET | `/about` | Tentang prodi |
| GET | `/catalog` | Katalog artikel |
| GET | `/articles/{slug}` | Detail artikel |
| GET | `/review` | Daftar review |

### User terautentikasi (`/articles`)
| Method | URI | Fungsi |
|--------|-----|--------|
| GET | `/articles/my` | Artikel Saya |
| GET/POST | `/articles/create`, `/articles` | Tulis artikel baru |
| GET/PUT | `/articles/{article}/edit` | Edit (artikel live → jadi usulan perubahan) |
| DELETE | `/articles/{article}` | Hapus draft mandiri (tanpa approval admin) |
| PATCH | `/articles/{article}/request-delete` | Request hapus (untuk artikel Active) |
| GET | `/articles/{article}/preview` | Preview (termasuk draft belum tersimpan) |
| GET | `/articles/{article}/revisions` | Riwayat revisi |

### Admin (`/admin`)
| Method | URI | Fungsi |
|--------|-----|--------|
| GET | `/admin` | Dashboard |
| GET/POST | `/admin/articles` | Kelola artikel |
| GET | `/admin/articles/trash` | Trash artikel |
| PATCH | `/admin/articles/{article}/takedown` | Takedown artikel live |
| PATCH | `/admin/articles/{article}/approve-edit`, `/reject-edit` | Setujui/tolak usulan perubahan artikel live |
| GET/POST | `/admin/categories` | Kelola kategori |
| GET/POST | `/admin/dosen` | Kelola dosen |
| GET | `/admin/users` | Kelola user |
| GET | `/admin/comments` | Moderasi komentar + Kata Terlarang |
| POST/DELETE | `/admin/comments/banned-words` | Tambah/hapus kata terlarang |
| GET | `/admin/report` | Laporan |

## REST API

Base URL: `/api/v1` · Auth: `Authorization: Bearer {token}` (custom token, bukan Sanctum) · Format: JSON.

| Method | Endpoint | Auth | Fungsi |
|--------|----------|------|--------|
| GET | `/api/v1` | - | Health check |
| POST | `/api/v1/auth/register` | - | Registrasi |
| POST | `/api/v1/auth/login` | - | Login → token |
| POST | `/api/v1/auth/logout` | ✔ | Logout |
| GET | `/api/v1/auth/me` | ✔ | User saat ini |
| GET | `/api/v1/articles` | - | List artikel (`q`, `category`, `tag`, `sort`, `per_page`) |
| GET | `/api/v1/articles/{slug}` | - | Detail + artikel terkait |
| GET | `/api/v1/articles/{slug}/comments` | - | List komentar |
| POST | `/api/v1/articles/{slug}/comments` | ✔ | Kirim komentar |
| POST | `/api/v1/articles/{slug}/bookmark` | ✔ | Toggle bookmark |
| GET | `/api/v1/bookmarks` | ✔ | Bookmark user |
| GET | `/api/v1/categories` | - | List kategori |
| GET | `/api/v1/tags` | - | List tag |
| GET | `/api/v1/tags/{slug}/articles` | - | Artikel per tag |
| GET | `/api/v1/lecturers` | - | List dosen aktif |
| GET | `/api/v1/search?q=` | - | Full-text search (min. 2 karakter) |
| GET | `/api/v1/analytics/popular` | - | Artikel populer + statistik kategori |
| GET | `/api/v1/analytics/monthly` | - | Jumlah artikel per bulan |

Dokumentasi mesin (`GET /api/v1/docs`) tersedia secara live saat aplikasi berjalan. Untuk request lintas domain (frontend terpisah/mobile app), atur origin yang diizinkan lewat env `CORS_ALLOWED_ORIGINS` (lihat `config/cors.php`).

## Testing

```bash
php artisan test
```

Cakupan test (`tests/Feature`, `tests/Unit`):

- **Admin** — CRUD artikel, kategori, dosen, profil
- **Auth** — Alur login/register/logout web
- **Api** — Seluruh 17 endpoint REST API (auth, artikel, komentar, bookmark, taxonomy, search, analytics)
- **Security** — Header keamanan, otorisasi policy
- **Smoke / E2E** — Alur pengguna end-to-end
- **Unit/Models** — Relasi & scope model

## Database

9 tabel utama:

- **users** — Akun dengan role (admin/user)
- **categories** — Kategori artikel
- **articles** — Konten artikel (soft delete, slug, scheduled publishing, `trashed_reason`)
- **article_revisions** — Riwayat revisi & usulan perubahan artikel live (`status`: draft/pending/active/pending_edit/rejected)
- **lecturers** — Data dosen dengan status approval
- **reviews** — Review artikel/proyek
- **comments** — Komentar pada artikel (status pending/approved/rejected)
- **banned_words** — Daftar kata terlarang untuk filter komentar
- **pages** — Halaman CMS (homepage editable)
- **activity_logs** — Jejak aktivitas (polymorphic)

Detail lengkap schema, relasi, controller, dan view: lihat [`docs.md`](docs.md).

## Konfigurasi Database MySQL

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipedia
DB_USERNAME=root
DB_PASSWORD=your_password
# Opsional: batas waktu koneksi (detik) sebelum gagal cepat alih-alih
# menggantung sampai PHP max_execution_time habis. Default 5.
DB_CONNECT_TIMEOUT=5
```

## Deployment

```bash
composer install --optimize-autoloader --no-dev
npm install && npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Set `CORS_ALLOWED_ORIGINS` di `.env` production ke domain frontend yang diizinkan (default `*`).

Konfigurasi Nginx dan Apache tersedia di [`tutorial.md`](tutorial.md#14-deploy-ke-server-produksi).

## Dokumentasi

- [`tutorial.md`](tutorial.md) — Panduan instalasi lengkap untuk semua platform
- [`docs.md`](docs.md) — Dokumentasi teknis (database, routing, controller, view, middleware)

## License

MIT
