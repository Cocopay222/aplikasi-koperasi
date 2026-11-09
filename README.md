# SupportFlow

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)
[![JavaScript](https://img.shields.io/badge/JavaScript-323330?style=for-the-badge&logo=javascript&logoColor=F7DF1E)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)


</div>

---

## Tentang Proyek

**SupportFlow** adalah aplikasi web berbasis Laravel yang dirancang untuk memudahkan pengelolaan tiket support, pelaporan, dan rekapitulasi antar instansi atau koperasi. Sistem ini menyediakan solusi terpadu untuk manajemen tiket support dengan antarmuka yang intuitif dan responsif.

## Fitur Utama

### Multi-Role Authentication
- Sistem pembagian hak akses untuk **Tim Support** dan **Pelapor** (Koperasi/Instansi)
- Dashboard yang disesuaikan untuk masing-masing peran
- Manajemen akses berbasis role yang fleksibel

### Manajemen Laporan & Rekapitulasi
- Pembuatan dan pelacakan tiket bantuan yang terstruktur
- Sistem rekapitulasi otomatis untuk pelaporan berkala
- Respon dan penyelesaian tiket dengan status tracking real-time
- Export laporan dalam berbagai format

### Profil Identitas Digital
- Kartu identitas anggota digital terintegrasi
- Manajemen foto profil dan data pribadi
- Informasi koperasi dan nomor anggota
- Sistem verifikasi status anggota

### Personalisasi & Pengaturan
- **Tema Dinamis**: Mode Gelap & Mode Terang
- **Aksesibilitas**: Penyesuaian ukuran teks untuk kenyamanan membaca
- **Multibahasa**: Dukungan Bahasa Indonesia & Inggris
- Preferensi pengguna yang tersimpan otomatis

### Keamanan Akun
- Enkripsi password tingkat enterprise
- Validasi input untuk mencegah SQL Injection
- CSRF protection
- Session management yang aman

### UI/UX Modern
- Desain antarmuka yang **responsif** dan mobile-friendly
- Animasi transisi halus untuk pengalaman pengguna yang mulus
- Efek **glassmorphism** yang modern
- **Skeleton loading state** untuk feedback pengguna
- Desain yang intuitif dan user-friendly

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel (PHP) |
| **Database** | MySQL 8.0+ |
| **Frontend** | Blade Templating, Vanilla CSS, Vanilla JavaScript |
| **Package Manager** | Composer, NPM |

## Persyaratan Sistem

Sebelum memulai, pastikan Anda memiliki:

- **PHP** >= 8.0
- **Composer** >= 2.0
- **Node.js** >= 14.0 & **npm** >= 6.0
- **MySQL** >= 8.0
- **Git**

</div>

## Panduan Instalasi

### 1. Clone Repositori

```bash
git clone https://github.com/Cocopay222/aplikasi-koperasi.git
cd aplikasi-koperasi
```

### 2. Install Dependensi

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Konfigurasi Environment

```bash
# Salin file konfigurasi
cp .env.example .env

# Generate application key
php artisan key:generate
```

Kemudian edit file `.env` dan sesuaikan konfigurasi berikut:

```env
APP_NAME="SupportFlow"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=recap_support_tracker
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi Database

```bash
# Jalankan migrasi
php artisan migrate

# (Opsional) Seed data dummy untuk testing
php artisan db:seed
```

### 5. Jalankan Aplikasi

```bash
# Development server
php artisan serve

# Atau dengan port custom
php artisan serve --port=8001
```

Aplikasi akan dapat diakses di `http://localhost:8000`

### 6. Compile Assets (Opsional)

```bash
# Development build
npm run dev

# Production build
npm run build
```

## Konfigurasi Lanjutan

### Setup Database

```bash
# Buat database baru (jika belum ada)
mysql -u root -p -e "CREATE DATABASE recap_support_tracker;"

# Jalankan migrasi dengan seed
php artisan migrate:fresh --seed
```

### Konfigurasi Mail (Opsional)

Edit file `.env`:

```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@example.com
```

## Dokumentasi

### Struktur Direktori

```
aplikasi-koperasi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   └── ...
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
│   └── web.php
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── storage/
├── .env.example
├── composer.json
├── package.json
└── README.md
```

### Fitur Utama

- **Role Management**: Admin, Support Team, Pelapor
- **Ticket System**: Create, Read, Update, Delete tickets
- **Reporting**: Generate laporan terperinci
- **User Profile**: Manajemen profil dan identitas digital
- **Settings**: Personalisasi theme, language, dan preferences

## Penggunaan

### Login ke Sistem

1. Buka aplikasi di `http://localhost:8000`
2. Gunakan kredensial default (jika ada di seeder)
3. Pilih role: Support Team atau Pelapor

### Membuat Ticket Support

1. Login sebagai Pelapor
2. Navigasi ke "Buat Laporan"
3. Isi detail masalah/permintaan
4. Submit dan tunggu respon dari Support Team

### Tracking Status Ticket

1. Login dan buka "Dashboard"
2. Lihat status semua ticket pada "Riwayat Laporan"
3. Pantau progres secara real-time


## Commit Message Guidelines

Gunakan format conventional commits:

```
feat: Add new feature
fix: Fix bug
docs: Update documentation
style: Format code
refactor: Refactor code
test: Add tests
```

## Lapor Bug

Jika Anda menemukan bug, silakan buat [Issue](https://github.com/Cocopay222/aplikasi-koperasi/issues) dengan detail:

- Deskripsi bug
- Langkah-langkah reproduksi
- Expected behavior
- Actual behavior
- Screenshot (jika relevan)

## Lisensi

Proyek ini dilisensikan di bawah lisensi MIT - lihat file [LICENSE](LICENSE) untuk detail.

## Kontak & Support

- **Email**: [athafathiys@gmail.com](mailto:athafathiys@gmail.com)
- **GitHub Issues**: [Report an Issue](https://github.com/Cocopay222/aplikasi-koperasi/issues)
- **Discussions**: [Join Discussion](https://github.com/Cocopay222/aplikasi-koperasi/discussions)

---

<div align="center">

**[kembali ke atas](#supportflow)**

Dibuat dengan ❤️ untuk mempermudah operasional pengelolaan tiket layanan bantuan.

</div>
