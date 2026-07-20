# SI-Pedia — Dokumentasi Teknis Lengkap

> Catatan teknis internal SI-Pedia: arsitektur, schema, routing, controller, view, middleware, frontend, deployment.

---

## Daftar Isi

1. [Overview](#1-overview)
2. [Stack & Dependencies](#2-stack--dependencies)
3. [Struktur Folder](#3-struktur-folder)
4. [Database Schema](#4-database-schema)
5. [Model & Relationships](#5-model--relationships)
6. [Routing](#6-routing)
7. [Controllers](#7-controllers)
8. [Middleware](#8-middleware)
9. [Views & Components](#9-views--components)
10. [Frontend & Styling](#10-frontend--styling)
11. [Authentication & Authorization](#11-authentication--authorization)
12. [File Upload](#12-file-upload)
13. [Activity Logging](#13-activity-logging)
14. [Seeder & Data Awal](#14-seeder--data-awal)
15. [Konfigurasi Environment](#15-konfigurasi-environment)
16. [Deployment](#16-deployment)
17. [Catatan Pengembangan](#17-catatan-pengembangan)

---

## 1. Overview

**SI-Pedia** adalah web application berbasis Laravel 13 yang berfungsi sebagai ensiklopedia digital untuk Program Studi Sistem Informasi, Universitas Indraprasta PGRI. Aplikasi ini memungkinkan admin untuk mengelola artikel, data dosen, kategori, halaman statis, dan review — sementara pengunjung umum dapat membaca artikel, berkomentar, dan melihat informasi dosen.

**Fitur utama:**
- Homepage dengan hero section, statistik, dan artikel terbaru
- Katalog artikel dengan filter kategori
- Detail artikel dengan sistem komentar
- Halaman About dengan informasi prodi
- Review system untuk artikel/proyek
- Admin Panel dengan dashboard statistik, grafik, dan activity log
- CRUD lengkap untuk artikel, kategori, dosen
- Manajemen user dengan role-based access
- Profil user dan admin
- Upload gambar untuk artikel, dosen, dan avatar
- Soft delete untuk artikel
- Bulk action pada artikel (publish, draft, delete)
- Scheduled publishing untuk artikel
- Sistem logging aktivitas

---

## 2. Stack & Dependencies

### Backend
| Komponen | Versi | Keterangan |
|----------|-------|------------|
| PHP | 8.3+ | Bahasa utama |
| Laravel | 13.x | Framework |
| SQLite | (bundled) | Database default |
| MySQL | 8.0+ | Database alternatif |

### Frontend
| Komponen | Versi | Keterangan |
|----------|-------|------------|
| Tailwind CSS | 4.x | Utility-first CSS (CSS-first config) |
| Alpine.js | 3.x (CDN) | Interaktivitas ringan |
| Vite | 6.x | Asset bundling |
| @tailwindcss/forms | 0.5.x | Form styling plugin |
| @tailwindcss/typography | 0.5.x | Prose styling plugin |
| @tailwindcss/vite | 4.x | Vite integration |
| axios | 1.x | HTTP client |

### Dev Dependencies
- PHPUnit 11.x (testing)
- FakerPHP (dummy data)
- Laravel Pint (code style)
- Laravel Pail (log viewer)
- Mockery (mocking)

---

## 3. Struktur Folder

```
si-pedia/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php
│   │   │   ├── ArticleController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── CommentController.php
│   │   │   ├── Controller.php          ← base class + logActivity()
│   │   │   ├── DosenController.php
│   │   │   ├── HomepageController.php
│   │   │   ├── PageController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ReviewController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── Concerns/
│       │   └── HasSlug.php             ← trait auto-generate slug
│       ├── ActivityLog.php
│       ├── Article.php
│       ├── Category.php
│       ├── Comment.php
│       ├── Lecturer.php
│       ├── Page.php
│       ├── Review.php
│       └── User.php
├── bootstrap/
│   └── app.php                         ← Laravel 11+ config (middleware, routing)
├── database/
│   ├── database.sqlite                 ← SQLite database file
│   ├── migrations/                     ← 13 migration files
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   ├── build/                          ← Vite output (hashed assets)
│   ├── index.php                       ← Entry point
│   └── *.png                           ← Static images
├── resources/
│   ├── css/
│   │   └── app.css                     ← Tailwind v4 + design tokens
│   ├── js/
│   │   └── app.js                      ← Axios setup
│   └── views/
│       ├── auth/                       ← 5 auth views
│       ├── components/                 ← 6 reusable components
│       ├── errors/                     ← 3 error pages
│       ├── layouts/
│       │   └── app.blade.php           ← Layout pakai Poppins
│       ├── components/
│       │   └── layouts/
│       │       └── app.blade.php       ← Layout pakai Inter + SEO/OG
│       └── pages/                      ← 17 page views
├── routes/
│   └── web.php                         ← Semua route
├── vite.config.js                      ← Vite config (base: /sipedia/build/)
├── composer.json
├── package.json
├── .env.example
├── tutorial.md                         ← Panduan install
└── docs.md                             ← Dokumentasi ini
```

---

## 4. Database Schema

### Tabel Domain (8 tabel)

#### `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto-increment |
| name | string | Nama lengkap |
| username | string, nullable | Username unik |
| email | string, unique | Email untuk login |
| email_verified_at | timestamp, nullable | Waktu verifikasi email |
| password | string | Hashed password |
| role | enum('admin','user') | Default: 'user' |
| study_program | string, nullable | Program studi |
| force | string, nullable | Angkatan |
| avatar | string, nullable | Path foto profil |
| remember_token | string | Remember me token |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `categories`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto-increment |
| name | string | Nama kategori |
| color | string | Default: '#8cbcff' |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `articles`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto-increment |
| title | string | Judul artikel |
| slug | string, unique, nullable | URL-friendly identifier (auto-generated) |
| category_id | bigint FK, nullable | → categories.id (null on delete) |
| writer | string | Default: 'Admin' |
| status | enum('active','draft') | Default: 'active' |
| content | longtext, nullable | Isi artikel (HTML) |
| image | string, nullable | Path gambar thumbnail |
| views | unsigned bigint | Default: 0 |
| scheduled_at | timestamp, nullable | Jadwal publish |
| created_at | timestamp | |
| updated_at | timestamp | |
| deleted_at | timestamp, nullable | Soft delete |

#### `lecturers`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto-increment |
| full_name | string, nullable | Nama lengkap |
| nidn | string, nullable | Nomor Induk Dosen Nasional |
| nip | string, nullable | Nomor Induk Pegawai |
| username | string, nullable | Username |
| email | string, nullable | Email |
| phone | string, nullable | Telepon |
| address | string, nullable | Alamat |
| place_of_birth | string, nullable | Tempat lahir |
| date_of_birth | date, nullable | Tanggal lahir |
| gender | string, nullable | Jenis kelamin |
| study_program | string, nullable | Program studi |
| expertise | string, nullable | Bidang keahlian |
| photo | string, nullable | Path foto |
| status | enum('active','waiting','rejected') | Default: 'waiting' |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `reviews`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto-increment |
| title | string | Judul review |
| type | string, nullable | Tipe (misal: 'Social media') |
| description | text, nullable | Deskripsi |
| image | string, nullable | Gambar |
| avatar | string, nullable | Avatar pengirim |
| views | unsigned bigint | Default: 0 |
| status | enum('pending','accepted','declined') | Default: 'pending' |
| reviewed_at | date, nullable | Tanggal direview |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `pages`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto-increment |
| name | string | Identifier unik (misal: 'home') |
| title | string, nullable | Judul halaman |
| banner | string, nullable | Path banner image |
| content | longtext, nullable | Konten HTML |
| status | enum('draft','publish') | Default: 'draft' |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `comments`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto-increment |
| article_id | bigint FK | → articles.id (cascade on delete) |
| user_id | bigint FK | → users.id (cascade on delete) |
| content | text | Isi komentar |
| status | enum('pending','approved','hidden') | Default: 'pending' |
| created_at | timestamp | |
| updated_at | timestamp | |

#### `activity_logs`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | Auto-increment |
| user_id | bigint FK, nullable | → users.id (null on delete) |
| action | string | Jenis aksi (misal: 'create', 'update', 'delete') |
| description | text | Deskripsi aktivitas |
| subject_type | string, nullable | Polymorphic: tipe model |
| subject_id | unsigned bigint, nullable | Polymorphic: ID model |
| ip_address | string, nullable | IP pelaku |
| created_at | timestamp | |
| updated_at | timestamp | |

### Tabel Framework (7 tabel)
- `sessions` — Session storage (database driver)
- `password_reset_tokens` — Token reset password
- `cache` — Cache storage
- `cache_locks` — Cache locking
- `jobs` — Queue jobs
- `job_batches` — Batch jobs
- `failed_jobs` — Failed queue jobs

### Relasi Database

```
categories ←── articles ──→ comments
                              ↓
                            users

users ←── activity_logs (polymorphic: bisa ke model manapun)
```

- **articles.category_id** → categories.id (nullable, null on delete)
- **comments.article_id** → articles.id (cascade on delete)
- **comments.user_id** → users.id (cascade on delete)
- **activity_logs.user_id** → users.id (nullable, null on delete)
- **activity_logs.subject** → polymorphic (subject_type + subject_id)

---

## 5. Model & Relationships

### `Article`
- **Traits:** HasFactory, SoftDeletes
- **Fillable:** title, slug, category_id, writer, status, content, image, views, scheduled_at
- **Relationships:**
  - `category()` → BelongsTo(Category)
  - `comments()` → HasMany(Comment), ordered by latest
- **Slug:** Auto-generated dari `title` via trait `HasSlug`

### `Category`
- **Traits:** HasFactory
- **Fillable:** name, color
- **Relationships:**
  - `articles()` → HasMany(Article)

### `Comment`
- **Traits:** HasFactory
- **Fillable:** article_id, user_id, content, status
- **Relationships:**
  - `article()` → BelongsTo(Article)
  - `user()` → BelongsTo(User)

### `User`
- **Traits:** HasFactory, Notifiable
- **Implements:** MustVerifyEmail
- **Fillable:** name, username, email, password, role, study_program, force, avatar
- **Hidden:** password, remember_token
- **Casts:** email_verified_at → datetime, password → hashed

### `Lecturer`
- **Traits:** HasFactory
- **Fillable:** full_name, nidn, nip, username, email, phone, address, place_of_birth, date_of_birth, gender, study_program, expertise, photo, status
- **Relationships:** none

### `Review`
- **Traits:** HasFactory
- **Fillable:** title, type, description, image, avatar, views, status, reviewed_at
- **Relationships:** none

### `Page`
- **Traits:** HasFactory
- **Fillable:** name, title, banner, content, status
- **Relationships:** none

### `ActivityLog`
- **Fillable:** user_id, action, description, subject_type, subject_id, ip_address
- **Relationships:**
  - `user()` → BelongsTo(User)
  - `subject()` → MorphTo (polymorphic)

### Trait: `HasSlug`
- Lokasi: `app/Models/Concerns/HasSlug.php`
- Otomatis generate slug dari `title` saat model dibuat
- Regenerate slug saat `title` berubah (jika `slug` tidak di-set manual)
- Pastikan slug unik dengan suffix `-1`, `-2`, dst.

---

## 6. Routing

Semua route didefinisikan di `routes/web.php`.

### Route Publik

| Method | URI | Controller@Method | Name |
|--------|-----|-------------------|------|
| GET | `/` | PageController@home | `home` |
| GET | `/about` | PageController@about | `about` |
| GET | `/catalog` | PageController@catalog | `catalog` |
| GET | `/articles/{article:slug}` | PageController@showArticle | `articles.show` |
| GET | `/review` | ReviewController@index | `review.index` |
| GET | `/articles/{article:slug}/comments` | CommentController@index | `comments.index` |

### Route Auth (guest only)

| Method | URI | Controller@Method | Name | Middleware |
|--------|-----|-------------------|------|------------|
| GET | `/login` | AuthController@showLogin | `login` | guest |
| POST | `/login` | AuthController@login | — | guest, throttle:5,1 |
| GET | `/register` | AuthController@showRegister | `register` | guest |
| POST | `/register` | AuthController@register | — | guest, throttle:3,1 |
| GET | `/forgot-password` | AuthController@showForgot | `password.request` | guest |
| POST | `/forgot-password` | AuthController@sendResetLink | `password.email` | guest, throttle:3,1 |
| GET | `/reset-password/{token}` | AuthController@showResetForm | `password.reset` | guest |
| POST | `/reset-password` | AuthController@resetPassword | `password.update` | guest, throttle:3,1 |

### Route Auth (authenticated)

| Method | URI | Controller@Method | Name | Middleware |
|--------|-----|-------------------|------|------------|
| POST | `/logout` | AuthController@logout | `logout` | auth |
| POST | `/articles/{article}/comments` | CommentController@store | `comments.store` | auth, throttle:10,1 |

### Route Email Verification

| Method | URI | Name | Middleware |
|--------|-----|------|------------|
| GET | `/email/verify` | `verification.notice` | auth |
| GET | `/email/verify/{id}/{hash}` | `verification.verify` | auth, signed |
| POST | `/email/verification-notification` | `verification.send` | auth, throttle:6,1 |

### Route Profil

| Method | URI | Controller@Method | Name | Middleware |
|--------|-----|-------------------|------|------------|
| GET | `/profile` | ProfileController@show | `profile.show` | auth, verified |
| GET | `/profile/edit` | ProfileController@edit | `profile.edit` | auth, verified |
| PUT | `/profile` | ProfileController@update | `profile.update` | auth, verified |

### Route Admin (prefix: `/admin`)

| Method | URI | Controller@Method | Name |
|--------|-----|-------------------|------|
| GET | `/admin/` | PageController@adminPanel | `admin.panel` |
| GET | `/admin/report` | PageController@reportPosts | `admin.report` |
| **Artikel** | | | |
| GET | `/admin/articles` | ArticleController@index | `admin.articles.index` |
| GET | `/admin/articles/create` | ArticleController@create | `admin.articles.create` |
| POST | `/admin/articles` | ArticleController@store | `admin.articles.store` |
| GET | `/admin/articles/{article}/edit` | ArticleController@edit | `admin.articles.edit` |
| PUT | `/admin/articles/{article}` | ArticleController@update | `admin.articles.update` |
| DELETE | `/admin/articles/{article}` | ArticleController@destroy | `admin.articles.destroy` |
| PATCH | `/admin/articles/{article}/bulk` | ArticleController@bulkAction | `admin.articles.bulk` |
| **Review** | | | |
| PATCH | `/admin/reviews/{review}/accept` | ReviewController@accept | `admin.reviews.accept` |
| PATCH | `/admin/reviews/{review}/decline` | ReviewController@decline | `admin.reviews.decline` |
| **Homepage** | | | |
| GET | `/admin/homepage/edit` | HomepageController@edit | `admin.homepage.edit` |
| PUT | `/admin/homepage` | HomepageController@update | `admin.homepage.update` |
| GET | `/admin/pages/create` | HomepageController@createPage | `admin.pages.create` |
| POST | `/admin/pages` | HomepageController@storePage | `admin.pages.store` |
| **Kategori** | | | |
| GET | `/admin/categories` | CategoryController@index | `admin.categories.index` |
| POST | `/admin/categories` | CategoryController@store | `admin.categories.store` |
| DELETE | `/admin/categories/{category}` | CategoryController@destroy | `admin.categories.destroy` |
| **User** | | | |
| GET | `/admin/users` | UserController@index | `admin.users.index` |
| PATCH | `/admin/users/{user}/role` | UserController@updateRole | `admin.users.updateRole` |
| **Dosen** | | | |
| GET | `/admin/dosen` | DosenController@index | `admin.dosen.index` |
| GET | `/admin/dosen/create` | DosenController@create | `admin.dosen.create` |
| POST | `/admin/dosen` | DosenController@store | `admin.dosen.store` |
| GET | `/admin/dosen/{lecturer}/edit` | DosenController@edit | `admin.dosen.edit` |
| PUT | `/admin/dosen/{lecturer}` | DosenController@update | `admin.dosen.update` |
| GET | `/admin/dosen/{lecturer}/acc` | DosenController@acc | `admin.dosen.acc` |
| PATCH | `/admin/dosen/{lecturer}/approve` | DosenController@approve | `admin.dosen.approve` |
| DELETE | `/admin/dosen/{lecturer}` | DosenController@destroy | `admin.dosen.destroy` |

Semua route admin dilindungi middleware `auth` + `admin`.

---

## 7. Controllers

### `Controller` (Base)
- Lokasi: `app/Http/Controllers/Controller.php`
- Menggunakan trait: `AuthorizesRequests`, `ValidatesRequests`
- Helper method: `logActivity($action, $description, $subject = null)` — membuat record `ActivityLog` dengan data user, aksi, deskripsi, polymorphic subject, dan IP address

### `PageController`
Menangani semua halaman publik dan dashboard admin.

| Method | Deskripsi | Data yang dikirim ke view |
|--------|-----------|--------------------------|
| `home()` | Homepage | `$articles` (6 terbaru, status=active), `$page` (name='home', status='publish') |
| `about()` | Halaman About | Tidak ada data dinamis |
| `catalog()` | Katalog artikel | `$articles` (paginated 12, status=active, scheduled_at <= now) |
| `showArticle(Article)` | Detail artikel | `$article` (with approved comments + user) |
| `adminPanel()` | Dashboard admin | `$stats` (articles/lecturers/reviews/users count), `$articles` (10 terbaru), `$monthlyArticles`, `$recentActivities` |
| `reportPosts()` | Laporan artikel | `$stats` (total/active/draft/deleted/scheduled), `$articles` (10 terbaru), `$recentActivities` |

### `ArticleController`
CRUD artikel (admin only).

| Method | Deskripsi | Validasi |
|--------|-----------|----------|
| `index(Request)` | Daftar artikel (paginated 10, searchable) | — |
| `create()` | Form buat artikel | — |
| `store(Request)` | Simpan artikel baru | title, category_id, writer, status, content (required); image (nullable, max 10MB); created_at (required date); scheduled_at (nullable date) |
| `edit(Article)` | Form edit artikel | — |
| `update(Request, Article)` | Update artikel | Same as store |
| `destroy(Article)` | Hapus artikel (soft delete) | — |
| `bulkAction(Request)` | Bulk publish/draft/delete | ids (array, exists), action (in: publish,draft,delete) |

### `CategoryController`
Manajemen kategori (admin only).

| Method | Deskripsi | Validasi |
|--------|-----------|----------|
| `index()` | Daftar kategori dengan jumlah artikel | — |
| `store(Request)` | Buat kategori baru | name (required, unique) |
| `destroy(Category)` | Hapus kategori | — |

### `CommentController`
Sistem komentar.

| Method | Deskripsi | Validasi |
|--------|-----------|----------|
| `index(Request)` | Ambil komentar artikel (JSON) | — |
| `store(Request, Article)` | Buat komentar | content (required, max 1000 chars) |

### `DosenController`
Manajemen dosen (admin only).

| Method | Deskripsi | Validasi |
|--------|-----------|----------|
| `index(Request)` | Daftar dosen (paginated 5, searchable) | — |
| `create()` | Form buat dosen | — |
| `store(Request)` | Simpan dosen baru | nidn, username, address (required); photo (nullable, max 10MB) |
| `edit(Lecturer)` | Form edit dosen | — |
| `update(Request, Lecturer)` | Update dosen | Same as store |
| `approve(Lecturer)` | Setujui dosen (status → 'active') | — |
| `acc(Lecturer)` | Halaman detail persetujuan | — |
| `destroy(Lecturer)` | Hapus dosen | — |

### `HomepageController`
Manajemen konten homepage (admin only).

| Method | Deskripsi | Validasi |
|--------|-----------|----------|
| `edit()` | Form edit homepage | — |
| `update(Request)` | Update konten homepage | title (required), content (required), banner (nullable, max 10MB) |
| `createPage()` | Form buat halaman baru | — |
| `storePage(Request)` | Simpan halaman baru | name (required, unique), title (required), content (required), status (in: active,draft) |

### `ReviewController`
Sistem review.

| Method | Deskripsi | Validasi |
|--------|-----------|----------|
| `index(Request)` | Daftar review publik (paginated 8, searchable, filterable) | — |
| `accept(Review)` | Terima review (admin) | — |
| `decline(Review)` | Tolak review (admin) | — |

### `ProfileController`
Manajemen profil user.

| Method | Deskripsi | Validasi |
|--------|-----------|----------|
| `show()` | Tampilkan profil (admin → profil_admin, user → profil_user) | — |
| `edit()` | Form edit profil | — |
| `update(Request)` | Update profil | email (required, unique), username (nullable), password (nullable, min 6), avatar (nullable, max 10MB) |

### `AuthController`
Autentikasi.

| Method | Deskripsi | Validasi |
|--------|-----------|----------|
| `showLogin()` | Form login | — |
| `login(Request)` | Proses login | email (required), password (required) |
| `showRegister()` | Form register | — |
| `register(Request)` | Proses register | name, email (unique), password (confirmed, min 6), terms (accepted) |
| `showForgot()` | Form lupa password | — |
| `sendResetLink(Request)` | Kirim link reset | email (required) |
| `showResetForm(token)` | Form reset password | — |
| `resetPassword(Request)` | Proses reset | token, email, password (confirmed, min 6) |
| `logout(Request)` | Logout | — |

### `UserController`
Manajemen user (admin only).

| Method | Deskripsi |
|--------|-----------|
| `index()` | Daftar user (paginated 15) |
| `updateRole(Request, User)` | Update role user |

---

## 8. Middleware

### Custom Middleware

**`AdminMiddleware`** — `app/Http/Middleware/AdminMiddleware.php`
- Dicek: `auth()->check() && auth()->user()->role === 'admin'`
- Jika bukan admin → abort 403 ("Akses ditolak. Anda bukan admin.")
- Terdaftar di `bootstrap/app.php` sebagai alias `'admin'`

### Middleware Built-in Laravel

| Middleware | Fungsi | Diterapkan ke |
|------------|--------|---------------|
| `guest` | Hanya bisa diakses user belum login | Login, Register, Forgot/Reset Password |
| `auth` | Wajib login | Logout, Comments, Profile, Email verification, Semua admin routes |
| `verified` | Email harus terverifikasi | Profile routes |
| `admin` | Role harus 'admin' | Semua `/admin/*` routes |
| `throttle:5,1` | Rate limit 5 request/menit | POST /login |
| `throttle:3,1` | Rate limit 3 request/menit | Register, Forgot/Reset Password |
| `throttle:10,1` | Rate limit 10 request/menit | POST comment |
| `throttle:6,1` | Rate limit 6 request/menit | Email verification |
| `signed` | URL harus signed | Email verification link |

---

## 9. Views & Components

### Layout

**`layouts/app.blade.php`** — Layout utama
- Font: **Poppins** (300–900) dari fonts.bunny.net
- Props: `active` (active nav), `footer` ('full'/'min'/'none'), `title`
- Load: app.css + app.js via Vite
- Body: `bg-white font-sans text-gray-900 antialiased`
- Include: `<x-navbar>`, conditional `<x-footer>` / `<x-footer-min>`

**`components/layouts/app.blade.php`** — Layout alternatif
- Font: **Inter** (400–700)
- Include: Alpine.js (CDN), SEO meta/OG tags
- Props: `title`, `meta_description`, `og_image`
- Dark mode support

### Komponen

| Komponen | File | Fungsi |
|----------|------|--------|
| Navbar | `components/navbar.blade.php` | Navigasi utama, dropdown user, login/register button |
| Footer | `components/footer.blade.php` | Footer lengkap 4 kolom (brand, explore, support, contact) |
| Footer Min | `components/footer-min.blade.php` | Footer minimal (logo + nama) |
| Flash Messages | `components/flash-messages.blade.php` | Toast notifikasi auto-dismiss 5 detik (Alpine.js) |
| Auth Layout | `components/auth-layout.blade.php` | Split layout untuk halaman login/register |
| Cap | `components/cap.blade.php` | SVG ikon toga wisuda |

### Halaman Publik (6 views)

| View | File | Deskripsi |
|------|------|-----------|
| Homepage | `pages/homepage.blade.php` | Hero section + statistik + artikel terbaru + kategori + CTA |
| Catalog | `pages/catalog.blade.php` | Grid 3 kolom artikel dengan pagination |
| About | `pages/about.blade.php` | Info prodi (hardcoded: timeline, dosen, visi misi) |
| Article Detail | `pages/article_detail.blade.php` | Artikel lengkap + komentar + form komentar |
| Review | `pages/review.blade.php` | Listing review dengan search & filter |
| User Profile | `pages/profil_user.blade.php` | Profil user (read-only + edit button) |
| Admin Profile | `pages/profil_admin.blade.php` | Profil admin (read-only + edit button) |

### Halaman Admin (10 views)

| View | File | Deskripsi |
|------|------|-----------|
| Admin Panel | `pages/admin_panel.blade.php` | Dashboard: stats, grafik bulanan, tabel artikel, fast action, activity log |
| Article Index | `pages/article_index.blade.php` | Tabel artikel dengan edit/delete |
| Edit Article | `pages/edit_article.blade.php` | Form create/edit artikel |
| Manage Category | `pages/manage_category.blade.php` | Form tambah + daftar kategori |
| Dosen Index | `pages/dosen_index.blade.php` | Tabel dosen dengan search |
| Dosen Create | `pages/dosen_create.blade.php` | Form create/edit dosen |
| Dosen ACC | `pages/dosen_acc.blade.php` | Halaman persetujuan dosen |
| Manage Users | `pages/manage_users.blade.php` | Tabel user (read-only) |
| Report Posts | `pages/report_posts.blade.php` | Statistik + tabel artikel + activity log |
| Edit Homepage | `pages/edit_homepage.blade.php` | Form edit hero section |
| Create Page | `pages/create_page.blade.php` | Form buat halaman CMS |

### Halaman Auth (5 views)

| View | File | Deskripsi |
|------|------|-----------|
| Login | `auth/login.blade.php` | Form email + password + remember |
| Register | `auth/register.blade.php` | Form name + email + password + terms |
| Forgot Password | `auth/forgot-password.blade.php` | Form email reset |
| Reset Password | `auth/reset-password.blade.php` | Form new password |
| Verify Email | `auth/verify-email.blade.php` | Prompt verifikasi email |

### Error Pages (3 views)

| View | File | Deskripsi |
|------|------|-----------|
| 403 | `errors/403.blade.php` | "Akses Ditolak" |
| 404 | `errors/404.blade.php` | "Halaman Tidak Ditemukan" |
| 500 | `errors/500.blade.php` | "Terjadi Kesalahan" |

---

## 10. Frontend & Styling

### Tailwind CSS v4 (CSS-first Config)

Konfigurasi tema ada di `resources/css/app.css` menggunakan sintaks `@theme {}` Tailwind v4.

### Design Tokens

**Warna:**
| Token | Nilai | Penggunaan |
|-------|-------|------------|
| `ink-800` – `ink-950` | `#07081f` → `#14163f` | Background dark/navy |
| `brand-50` – `brand-700` | `#eef4fb` → `#2a589a` | Primary blue |
| `accent-500` – `accent-700` | `#5b2ee6` → `#3a12b3` | Accent indigo |
| `status-active` | `#5d3da2` | Status aktif |
| `badge-cat` | `#8cbcff` | Badge kategori |
| `edit` | `#ffd400` | Tombol edit |
| `danger` | `#ff1014` | Tombol hapus/error |
| `field` | `#c4c4c4` | Input field border |
| `tablehead` | `#d9d9d9` | Table header bg |
| `profilebg` | `#f5e6f0` | Profile background |
| `userbadge` | `#e3c8f6` | User badge |

**Warna hardcoded di blade:**
- `#0a0b2f` — Ink/navy untuk text
- `#336cbc` — Brand blue untuk link/button
- `#2563eb` — Hover blue

**Font:**
- **Poppins** (layout utama) — weights: 300, 400, 500, 600, 700, 800, 900
- **Inter** (layout alternatif) — weights: 400, 500, 600, 700

### Vite Configuration

File: `vite.config.js`
- Base path: `/sipedia/build/` (untuk deployment subdirectory)
- Plugins: `laravel-vite-plugin`, `@tailwindcss/vite`
- Entry points: `resources/css/app.css`, `resources/js/app.js`

### JavaScript

File: `resources/js/app.js`
- Import axios, set as `window.axios`
- Default header: `X-Requested-With: XMLHttpRequest`
- Alpine.js dimuat via CDN di component layout

### Responsive Breakpoints

Menggunakan default Tailwind:
- `sm:` — 640px
- `md:` — 768px
- `lg:` — 1024px
- `xl:` — 1280px

---

## 11. Authentication & Authorization

### Sistem Autentikasi

- **Driver:** Session-based (database sessions)
- **Email Verification:** Wajib via `MustVerifyEmail` interface
- **Password Reset:** Via email token
- **Remember Me:** Didukung

### Sistem Authorization

Dua role:
1. **`user`** — Biasa, bisa baca artikel, komentar, edit profil
2. **`admin`** — Full akses ke admin panel

### Proteksi:

| Akses | Mekanisme |
|-------|-----------|
| Halaman publik | Tidak ada proteksi |
| Komentar | Wajib login + throttle |
| Profil | Wajib login + email terverifikasi |
| Admin Panel | Wajib login + role admin |
| Guest pages | Hanya bisa diakses belum login |

### Akun Default (seeder)

| Role | Email | Password | Detail |
|------|-------|----------|--------|
| Admin | adminSIPedia@gmail.com | `password` | — |
| User | ucupganteng@gmail.com | `password` | study_program: Sistem Informasi, force: 2024 |

---

## 12. File Upload

### Konfigurasi
- Driver: `local` (storage/app/public)
- Max size: 10MB per file
- Format: JPG, PNG, WEBP
- Disimpan di: `storage/app/public/`

### Upload Points

| Fitur | Field | Path |
|-------|-------|------|
| Artikel | `image` | storage/app/public/ |
| Dosen | `photo` | storage/app/public/ |
| Profil | `avatar` | storage/app/public/ |
| Homepage | `banner` | storage/app/public/ |

### Akses File

Untuk mengakses file yang di-upload di production:
```bash
php artisan storage:link
```

Di blade template, gunakan:
```php
<img src="{{ Storage::url($article->image) }}">
```

---

## 13. Activity Logging

### Cara Kerja

Setiap aksi admin di-log melalui method `logActivity()` di base Controller:

```php
$this->logActivity(
    action: 'create',
    description: 'membuat artikel baru: Judul Artikel',
    subject: $article  // optional, untuk polymorphic relation
);
```

### Data yang disimpan:
- `user_id` — Siapa yang melakukan
- `action` — Jenis aksi (create, update, delete, approve, dll)
- `description` — Deskripsi dalam Bahasa Indonesia
- `subject_type` + `subject_id` — Model yang terkait (polymorphic)
- `ip_address` — IP pelaku
- `created_at` — Kapan dilakukan

### Ditampilkan di:
- Admin Panel → "Recent Activity" section
- Report Posts → "Recent Activity" section

---

## 14. Seeder & Data Awal

File: `database/seeders/DatabaseSeeder.php`

### Data yang di-seed:

**Users (2):**
- Admin: `adminSIPedia@gmail.com` / `password` / role: `admin`
- User: `ucupganteng@gmail.com` / `password` / role: `user` / study_program: Sistem Informasi / force: 2024

**Categories (4):**
- Berita, Event, Akademik, Lomba

**Articles (4):**
- 4 artikel contoh, masing-masing terkait ke 1 kategori, status: active, writer: Admin

**Lecturers (5):**
- 5 data dosen contoh dengan NIDN, username, alamat, status: active

**Reviews (8):**
- 8 review contoh dengan status: pending

### Perintah:

```bash
# Jalankan semua seeder
php artisan db:seed

# Reset database + seed ulang
php artisan migrate:fresh --seed

# Jalankan seeder tertentu
php artisan db:seed --class=DatabaseSeeder
```

---

## 15. Konfigurasi Environment

File `.env.example` sudah disediakan. Key settings:

```env
APP_NAME="SI-Pedia"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

# Database SQLite (default)
DB_CONNECTION=sqlite

# Database MySQL (alternatif)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sipedia
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

### Variabel Penting:

| Variabel | Fungsi | Default |
|----------|--------|---------|
| `APP_ENV` | Lingkungan (local/production) | local |
| `APP_DEBUG` | Tampilkan error detail | true |
| `APP_URL` | Base URL aplikasi | http://localhost:8000 |
| `APP_KEY` | Encryption key (auto-generate) | — |
| `DB_CONNECTION` | Database driver (sqlite/mysql) | sqlite |
| `SESSION_DRIVER` | Session storage | database |
| `CACHE_STORE` | Cache storage | database |

---

## 16. Deployment

### Production Checklist

```bash
# 1. Set environment
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

# 2. Install dependencies (production only)
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Generate key
php artisan key:generate

# 4. Database
php artisan migrate --force

# 5. Storage link
php artisan storage:link

# 6. Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Permissions (Linux)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .
```

### Vite Base Path

Untuk deployment di subdirectory `/sipedia/`:
- `vite.config.js` sudah set `base: '/sipedia/build/'`
- Jika deployment di root domain, ubah ke `base: '/build/'`

### Server Requirements
- PHP 8.3+ dengan ekstensi: pdo_sqlite, mbstring, openssl, curl, gd, fileinfo
- Web server: Apache (mod_rewrite) atau Nginx
- SQLite atau MySQL

---

## 17. Catatan Pengembangan

### Hal yang perlu diperhatikan:

1. **Dua layout file** — `layouts/app.blade.php` (Poppins) dan `components/layouts/app.blade.php` (Inter + SEO). Kebanyakan pages menggunakan `<x-layouts.app>`.

2. **Slug generation** — Trait `HasSlug` otomatis generate slug dari title. Pastikan model Article menggunakan trait ini.

3. **Soft delete** — Article menggunakan soft delete. Data tidak hilang permanen, bisa di-restore.

4. **Moderasi komentar** — Komentar bisa auto-approved atau pending jika kena filter kata terlarang.

5. **Scheduled publishing** — Artikel dengan `scheduled_at` di masa depan tidak muncul di publik sampai waktunya tiba.

6. **Service providers** — Laravel 11+ bootstrap; cek `bootstrap/app.php` dan `app/Providers/`.

7. **Vite base path** — Diset untuk subdirectory `/sipedia/`. Sesuaikan jika deploy di root.

8. **SQLite default** — Tidak perlu install database server. Cocok untuk development dan deployment sederhana.

9. **Tailwind v4** — Tidak ada `tailwind.config.js`. Semua tema di `app.css` via `@theme {}`.

10. **Alpine.js via CDN** — Tidak di-bundle, dimuat langsung dari CDN di layout component.

### Struktur naming:

- **Route names:** `admin.{resource}.{action}` (contoh: `admin.articles.index`)
- **View paths:** `pages/{nama_halaman}` untuk pages, `auth/{nama_halaman}` untuk auth
- **Controller names:** `{Resource}Controller` (singular, PascalCase)

### Testing:

```bash
php artisan test
```

PHPUnit tersedia dengan konfigurasi default Laravel. Test files di `tests/` directory.

---

> Terakhir diperbarui: Juli 2026
