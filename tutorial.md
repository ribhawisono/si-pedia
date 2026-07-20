# SI-Pedia — Tutorial Instalasi

> Panduan install dan menjalankan SI-Pedia dari nol.

---

## Daftar Isi

1. [Tentang SI-Pedia](#1-tentang-si-pedia)
2. [Persyaratan Sistem](#2-persyaratan-sistem)
3. [Install PHP 8.3](#3-install-php-83)
4. [Install Composer](#4-install-composer)
5. [Install Node.js & npm](#5-install-nodejs--npm)
6. [Download Project](#6-download-project)
7. [Install Dependencies](#7-install-dependencies)
8. [Konfigurasi Environment](#8-konfigurasi-environment)
9. [Setup Database](#9-setup-database)
10. [Jalankan Migrasi & Seeder](#10-jalankan-migrasi--seeder)
11. [Build Frontend Assets](#11-build-frontend-assets)
12. [Jalankan Aplikasi](#12-jalankan-aplikasi)
13. [Akun Default](#13-akun-default)
14. [Deploy ke Server Produksi](#14-deploy-ke-server-produksi)
15. [Troubleshooting](#15-troubleshooting)

---

## 1. Tentang SI-Pedia

SI-Pedia adalah platform ensiklopedia digital untuk Program Studi Sistem Informasi, Universitas Indraprasta PGRI. Fitur utama:

- **Artikel** — Buat, edit, publish artikel dengan kategori dan gambar
- **Dosen** — Data dosen lengkap dengan foto, NIDN, dan bidang keahlian
- **Komentar** — Pengunjung bisa berkomentar di setiap artikel
- **Review** — Sistem review untuk artikel/proyek yang masuk
- **Admin Panel** — Dashboard lengkap untuk mengelola semua konten
- **Profil** — Profil user dan admin dengan upload avatar

**Stack teknologi:**
- Laravel 13 (PHP 8.3)
- Tailwind CSS v4
- SQLite (default) atau MySQL
- Vite 6 (asset bundling)

---

## 2. Persyaratan Sistem

### Minimum:
- **OS:** Windows 10/11, macOS 12+, atau Linux (Ubuntu 20.04+)
- **RAM:** 2 GB
- **Disk:** 500 MB kosong
- **Koneksi internet** (untuk download dependencies)

### Software yang harus terinstall:

| Software | Versi Minimum | Cek di Terminal |
|----------|---------------|-----------------|
| PHP | 8.3 | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js | 18.x | `node -v` |
| npm | 9.x | `npm -v` |

> **Belum punya?** Ikuti langkah di bawah ini satu per satu.

---

## 3. Install PHP 8.3

### Windows

1. Buka https://windows.php.net/download/
2. Download **PHP 8.3.x VS16 x64 Non Thread Safe** (zip)
3. Extract ke `C:\php`
4. Tambahkan `C:\php` ke PATH:
   - Buka **Settings** → **System** → **About** → **Advanced system settings**
   - Klik **Environment Variables**
   - Di bagian **System variables**, pilih `Path`, klik **Edit**
   - Klik **New**, ketik `C:\php`, klik **OK**
5. Buka CMD baru, verifikasi:
   ```
   php -v
   ```
6. Edit file `C:\php\php.ini` (copy dari `php.ini-development`):
   - Uncomment (hapus tanda `;`) baris ini:
     ```ini
     extension=pdo_sqlite
     extension=sqlite3
     extension=fileinfo
     extension=mbstring
     extension=openssl
     extension=curl
     extension=gd
     ```

### macOS

```bash
# Install Homebrew jika belum punya
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP 8.3
brew install php@8.3

# Tambahkan ke PATH
echo 'export PATH="/opt/homebrew/opt/php@8.3/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# Verifikasi
php -v
```

### Ubuntu / Debian

```bash
# Tambah repository PHP
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 + ekstensi yang dibutuhkan
sudo apt install -y php8.3 php8.3-cli php8.3-common php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-sqlite3 php8.3-mysql \
    php8.3-gd php8.3-bcmath php8.3-imagick php8.3-intl unzip

# Verifikasi
php -v
```

---

## 4. Install Composer

### Windows

1. Buka https://getcomposer.org/Composer-Setup.exe
2. Jalankan installer, ikuti wizard
3. Buka CMD baru, verifikasi:
   ```
   composer -V
   ```

### macOS / Linux

```bash
# Download dan install
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Verifikasi
composer -V
```

---

## 5. Install Node.js & npm

### Windows

1. Buka https://nodejs.org
2. Download **LTS version** (18.x atau lebih baru)
3. Jalankan installer, ikuti wizard (centang "Add to PATH")
4. Buka CMD baru, verifikasi:
   ```
   node -v
   npm -v
   ```

### macOS

```bash
brew install node

# Verifikasi
node -v
npm -v
```

### Ubuntu / Debian

```bash
# Install Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verifikasi
node -v
npm -v
```

---

## 6. Download Project

### Opsi A: Via ZIP

1. Minta file ZIP project dari developer
2. Extract ke folder yang diinginkan, misal:
   - Windows: `C:\Projects\si-pedia`
   - macOS/Linux: `~/si-pedia`

### Opsi B: Via Git

```bash
git clone <url-repository> si-pedia
cd si-pedia
```

---

## 7. Install Dependencies

Buka terminal / CMD, masuk ke folder project:

```bash
cd si-pedia
```

### Install PHP dependencies:

```bash
composer install
```

> Jika error "ext-* not found", pastikan ekstensi PHP sudah aktif (lihat langkah 3).

### Install JavaScript dependencies:

```bash
npm install
```

---

## 8. Konfigurasi Environment

### Buat file .env

```bash
# Linux / macOS
cp .env.example .env

# Windows
copy .env.example .env
```

### Generate application key

```bash
php artisan key:generate
```

### Edit file .env

Buka file `.env` dengan text editor (Notepad, VS Code, dll), pastikan isi seperti ini:

```env
APP_NAME="SI-Pedia"
APP_ENV=local
APP_KEY=xxx (otomatis terisi oleh langkah di atas)
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost:8000

# Database (SQLite — default, tidak perlu setup database server)
DB_CONNECTION=sqlite

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Locale Bahasa Indonesia
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

# Filesystem
FILESYSTEM_DISK=local
```

> **Ingin pakai MySQL?** Ubah bagian database:
> ```env
> DB_CONNECTION=mysql
> DB_HOST=127.0.0.1
> DB_PORT=3306
> DB_DATABASE=sipedia
> DB_USERNAME=root
> DB_PASSWORD=your_password
> ```

---

## 9. Setup Database

### Jika pakai SQLite (default):

```bash
# Buat file database kosong
touch database/database.sqlite
```

> Di Windows: cukup buat file kosong `database.sqlite` di folder `database/` pakai Notepad, Save as type: All Files.

### Jika pakai MySQL:

```sql
-- Login ke MySQL dulu
mysql -u root -p

-- Buat database
CREATE DATABASE sipedia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

---

## 10. Jalankan Migrasi & Seeder

### Buat semua tabel di database:

```bash
php artisan migrate
```

### Isi data awal (admin, kategori, artikel contoh, dosen contoh):

```bash
php artisan db:seed
```

> **Perhatian:** Seeder akan membuat akun admin dan data contoh. Jangan jalankan di production yang sudah punya data!

### Kalau mau reset database dari awal:

```bash
php artisan migrate:fresh --seed
```

---

## 11. Build Frontend Assets

### Untuk development (auto-reload saat edit):

```bash
npm run dev
```

### Untuk production (file lebih kecil & cepat):

```bash
npm run build
```

---

## 12. Jalankan Aplikasi

### Cara cepat (satu terminal):

```bash
php artisan serve
```

Aplikasi berjalan di: **http://localhost:8000**

### Cara lengkap (development dengan auto-reload):

Jalankan di **3 terminal berbeda**:

```bash
# Terminal 1 — Server
php artisan serve

# Terminal 2 — Queue worker (untuk background jobs)
php artisan queue:listen --tries=1

# Terminal 3 — Vite (auto-reload CSS/JS)
npm run dev
```

Atau jalankan sekaligus:

```bash
composer dev
```

---

## 13. Akun Default

Setelah menjalankan seeder, akun yang tersedia:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | adminSIPedia@gmail.com | `password` |
| **User** | ucupganteng@gmail.com | `password` |

### Login sebagai Admin:
1. Buka http://localhost:8000/login
2. Masukkan email admin + password
3. Klik **ACCESS**
4. Klik nama user di navbar → **Admin Panel**

### Login sebagai User biasa:
1. Buka http://localhost:8000/login
2. Masukkan email user + password
3. Atau register akun baru di http://localhost:8000/register

---

## 14. Deploy ke Server Produksi

### Persyaratan Server:
- PHP 8.3+
- Composer
- Node.js & npm (hanya untuk build, bisa dihapus setelah build)
- SQLite atau MySQL
- Web server (Apache / Nginx)

### Langkah Deploy:

```bash
# 1. Clone/upload project ke server
git clone <repo-url> /var/www/si-pedia
cd /var/www/si-pedia

# 2. Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# 3. Setup environment
cp .env.example .env
php artisan key:generate
# Edit .env: APP_ENV=production, APP_DEBUG=false, APP_URL=https://domain-anda.com

# 4. Setup database
php artisan migrate --force
php artisan db:seed --force

# 5. Storage link (agar gambar bisa diakses)
php artisan storage:link

# 6. Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Set permissions (Linux)
chown -R www-data:www-data /var/www/si-pedia
chmod -R 775 /var/www/si-pedia/storage
chmod -R 775 /var/www/si-pedia/bootstrap/cache
```

### Konfigurasi Nginx:

```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /var/www/si-pedia/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Konfigurasi Apache (.htaccess sudah disediakan Laravel):

Pastikan `mod_rewrite` aktif:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## 15. Troubleshooting

### "SQLSTATE: unable to open database file"
- Pastikan file `database/database.sqlite` ada dan bisa ditulis
- Cek permission: `chmod 664 database/database.sqlite`

### "Composer detected issues in your platform"
```bash
composer install --ignore-platform-reqs
```

### "Vite manifest not found"
```bash
npm run build
```
Atau pastikan `npm run dev` berjalan di terminal lain.

### "500 Server Error"
```bash
# Cek log
tail -f storage/logs/laravel.log

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Permission denied (Linux)
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Port sudah dipakai
```bash
# Jalankan di port lain
php artisan serve --port=8080
```

### "No application encryption key"
```bash
php artisan key:generate
```

### Upload gambar tidak muncul
```bash
php artisan storage:link
```

---

> **Butuh bantuan?** Hubungi developer untuk pertanyaan teknis lebih lanjut.
