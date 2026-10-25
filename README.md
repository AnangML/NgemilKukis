<p align="center">
  <img src="https://images.unsplash.com/photo-1499636136210-6f4ee915583e?auto=format&fit=crop&w=600&q=80" width="180" style="border-radius: 24px; box-shadow: 0 10px 25px rgba(45, 24, 12, 0.15);" alt="NgemilKukis Logo" />
</p>

<h1 align="center">🍪 NgemilKukis.</h1>

<p align="center">
  <strong>Artisan Soft-Baked Cookie Storefront & Comprehensive E-Commerce Management System</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x%20%7C%2013.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel" />
  <img src="https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js" />
  <img src="https://img.shields.io/badge/Xendit-Payment_Gateway-1E40AF?style=for-the-badge&logo=xendit&logoColor=white" alt="Xendit" />
  <img src="https://img.shields.io/badge/Tests-51%20Passed%20(100%25)-10B981?style=for-the-badge&logo=phpunit&logoColor=white" alt="PHPUnit Tests" />
</p>

---

## 📌 Sekilas Tentang Projek

**NgemilKukis** adalah platform toko online kukis artisanal (*soft-baked cookies*) modern yang dirancang untuk memberikan pengalaman berbelanja yang lezat, cepat, dan terpercaya bagi pelanggan, serta menyediakan sistem manajemen operasional internal yang komprehensif untuk pemilik bisnis, tim operasional dapur/penjual, dan tim customer service.

Dibangun dengan arsitektur **Laravel Monolith + Blade + Alpine.js** serta **Tailwind CSS v4**, aplikasi ini memadukan performa tinggi, keandalan proteksi transaksi, estetika desain visual premium berpedoman **Aturan 60-30-10**, dan sistem kontrol peran berbasis **RBAC (Role-Based Access Control)** yang lengkap dengan **Audit Trail (Log Aktivitas)**.

---

## ✨ Fitur-Fitur Utama

### 🛍️ 1. Storefront & Pengalaman Pelanggan
- **Katalog Kukis Interaktif**: Navigasi katalog yang responsif dengan filter kategori, pencarian real-time, badge khusus (*Best Seller*, *Seasonal*, *Limited Edition*), dan deskripsi rasa yang menggugah selera.
- **Cart Drawer Dinamis**: Keranjang belanja samping (*flyout drawer*) yang memungkinkan perubahan jumlah pesanan dan penambahan catatan khusus per pesanan tanpa memuat ulang halaman.
- **Personalisasi Kartu Ucapan (Gift Card)**: Fasilitas kado/hampers lengkap dengan pilihan template kartu ucapan, nama pengirim, nama penerima, dan pesan personal untuk momen spesial (Ulang Tahun, Lebaran, Natal, Kasih Sayang).
- **Fast 1-Click Checkout**: Formulir pemesanan cepat dengan proteksi **Idempotency Key** untuk mencegah transaksi ganda saat koneksi tidak stabil.
- **Integrasi Pembayaran Xendit**: Dukungan pembayaran otomatis via **QRIS**, Virtual Account (BCA, Mandiri, BNI, BRI), E-Wallet (OVO, DANA, ShopeePay), dan Kartu Kredit.
- **Simulator Pembayaran Lokal**: Endpoint simulasi pembayaran instan untuk mempermudah demonstrasi dan pengujian alur pesanan di lingkungan lokal tanpa kartu kredit / saldo live.
- **Dukungan Checkout Fleksibel**: Menerima pembelian oleh **Tamu (Guest Checkout)** maupun **Member Terdaftar**.

---

### 👑 2. Kukis VIP Club (Keanggotaan & 3 Metode Login)
- **Diskon Member Otomatis**: Potongan harga instan **Rp 10.000** pada setiap transaksi saat pelanggan berbelanja dalam keadaan login.
- **3 Opsi Masuk Cepat**:
  1. **Google Auth (1-Click Login)**: Masuk instan sekali klik menggunakan akun Google (dilengkapi simulator demo untuk lokal).
  2. **Email & Kata Sandi**: Registrasi akun mandiri klasik bagi pelanggan yang menyukai kredensial email.
  3. **Nomor Ponsel (Phone OTP)**: Masuk cepat menggunakan verifikasi nomor WhatsApp/HP.
- **Buku Alamat Pengiriman (Address Book)**: Simpan beberapa alamat kirim (Rumah, Kantor, Hampers Sahabat) untuk *1-click checkout* berikutnya.
- **Riwayat Pesanan Privat**: Pantau status pesanan yang sedang dipanggang, dikirim kurir, atau telah selesai secara terpusat di halaman akun pelanggan.

---

### 🛡️ 3. Panel Kontrol Internal & Role-Based Access Control (RBAC)
Portal staf internal terpadu di [`/admin/login`](http://localhost:8000/admin/login) dengan pembagian hak akses (*permission boundaries*) yang ketat:

| Fitur / Modul | Super Admin (`admin`) | Penjual / Dapur (`seller`) | Customer Service (`customer_service`) | Pelanggan (`customer`) |
| :--- | :---: | :---: | :---: | :---: |
| **Storefront & Belanja** | ✅ Akses | ✅ Akses | ✅ Akses | ✅ Akses Penuh |
| **Login Portal Internal** | ✅ Diizinkan | ✅ Diizinkan | ✅ Diizinkan | ❌ Ditolak (Diblokir) |
| **Dashboard Metrik Penjualan** | ✅ Semua Metrik | ✅ Metrik Pesanan & Stok | ✅ Ringkasan Pesanan | ❌ |
| **Katalog Produk & Atur Stok** | ✅ CRUD Lengkap | ✅ Tambah & Atur Stok | ❌ Dibatasi (Read-only) | ❌ |
| **Kelola Template Kartu Ucapan** | ✅ CRUD Lengkap | ✅ Tambah & Edit | ❌ Dibatasi (Read-only) | ❌ |
| **Kelola Pesanan & Status** | ✅ Kelola Penuh | ✅ Update Dapur & Kurir | ✅ Update Catatan / Komplain | 👁️ Pesanan Sendiri |
| **Pengaturan Tampilan & Brand** | ✅ Akses Penuh | ❌ Dilarang (403) | ❌ Dilarang (403) | ❌ |
| **Manajemen Akun Staf** | ✅ Kelola Penuh | ❌ Dilarang (403) | ❌ Dilarang (403) | ❌ |
| **Log Aktivitas (Audit Trail)** | ✅ Akses Penuh | ❌ Dilarang (403) | ❌ Dilarang (403) | ❌ |

---

### 🎨 4. Pengaturan Tampilan & Brand Dinamis ([`/admin/settings`](http://localhost:8000/admin/settings))
- **Upload Logo Toko**: Mendukung format `SVG, PNG, JPG, JPEG, WEBP, GIF, ICO, AVIF` dengan validasi batas memory **10 MB**.
- **Artisan Vector Fallback**: Jika logo custom belum diunggah, sistem otomatis menampilkan logo vektor kukis artisanal SVG bertekstur cokelat & emas.
- **Hero Banner & Teks Dinamis**: Ganti gambar banner utama, slogan toko, teks promo, dan nomor WhatsApp admin langsung dari UI panel tanpa mengubah kode template.

---

### 👥 5. Manajemen Staf ([`/admin/users`](http://localhost:8000/admin/users))
- Kelola seluruh akun pengguna internal.
- Filter tab cepat: *Semua Staf*, *Super Admin*, *Penjual*, *Customer Service*, dan *Pelanggan*.
- Modal pendaftaran staf baru dengan penentuan role langsung.
- Modal pembaruan data pengguna dan opsi reset kata sandi.
- Proteksi keamanan akun (Super Admin dilarang menghapus akunnya sendiri).

---

### 📋 6. Log Aktivitas & Audit Trail ([`/admin/activity-logs`](http://localhost:8000/admin/activity-logs))
- **Pencatatan Otomatis & Terpusat**: Mencatat siapa melakukan apa, kapan, pada data mana, alamat IP, dan user-agent perangkat.
- **Kategori Log**:
  - `auth`: Login staf, logout, login member (Google/Email/Phone), penolakan akses.
  - `order`: Pembuatan pesanan, konfirmasi pembayaran Webhook/Simulasi, dan perubahan status pesanan.
  - `catalog`: Penambahan produk, edit harga/stok, toggle ketersediaan etalase, dan kartu ucapan.
  - `settings`: Perubahan pengaturan brand, upload logo, hero banner, dan nomor kontak.
  - `staff`: Penambahan akun staf baru, perubahan peran (*role assignment*), dan penghapusan staf.
- **Filter Multi-Kriteria**: Pencarian teks, filter peran (*Super Admin, Penjual, CS, Pelanggan, Tamu*), kategori aksi, dan rentang tanggal (*Hari ini, Kemarin, 7 Hari, 30 Hari*).
- **Modal Diff Data**: Menampilkan parameter perubahan sebelum dan sesudah dalam format payload JSON yang rapi.

---

## 🎨 Palet Desain & Estetika (Aturan 60-30-10)

Desain antarmuka **NgemilKukis** menerapkan rasio warna profesional:
- **60% Latar Bersih (`#FFFFFF` / `#FCFAF7`)**: Memberikan kesan ruang lega, bersih, dan menonjolkan foto kelezatan kukis.
- **30% Cokelat Kukis Hangat (`#2D180C`)**: Digunakan untuk elemen struktur, kartu sidebar, teks utama, dan tombol primer.
- **10% Aksen Emas Kukis Artisanal (`#B87D4B`)**: Digunakan untuk badge, bintang rating, status aktif, dan aksen interaktif yang memikat mata (*call-to-action*).

---

## 🛠️ Arsitektur & Teknologi

- **Backend Framework**: [Laravel 12 / 13](https://laravel.com) (PHP 8.3+)
- **Database Engine**: SQLite (Default lokal) / MySQL / PostgreSQL
- **Frontend Layer**: Laravel Blade Templates + [Alpine.js 3.x](https://alpinejs.dev/)
- **CSS & Bundling**: [Tailwind CSS v4](https://tailwindcss.com/) + [Vite](https://vitejs.dev/)
- **Payment Gateway**: [Xendit PHP SDK](https://developers.xendit.co/) & Secure Webhook Signature
- **Code Quality & Testing**:
  - [PHPUnit 12](https://phpunit.de/) dengan **51 tests & 230 assertions (100% lolos)**
  - [Laravel Pint](https://laravel.com/docs/pint) untuk pemformatan kode standar PSR-12

---

## 🚀 Panduan Instalasi & Menjalankan Projek

### 1. Prasyarat Sistem
Pastikan perangkat Anda telah terpasang:
- **PHP** &ge; 8.3 (dengan ekstensi `pdo`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`/`imagick`)
- **Composer** &ge; 2.x
- **Node.js** &ge; 18.x & **npm**
- **Git**

---

### 2. Langkah-Langkah Instalasi

#### Langkah 1: Clone Repository
```bash
git clone https://github.com/AnangML/NgemilKukis.git
cd NgemilKukis
```

#### Langkah 2: Pasang Dependensi Backend (Composer)
```bash
composer install
```

#### Langkah 3: Pasang Dependensi Frontend (NPM) & Build Asset
```bash
npm install
npm run build
```
> *Untuk pengembangan aktif dengan hot-reload, jalankan `npm run dev`.*

#### Langkah 4: Setup File Environment
Salin file `.env.example` menjadi `.env` lalu generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

#### Langkah 5: Migrasi Database & Seeder Data Awal
Jalankan migrasi tabel beserta seeder katalog produk, template kartu ucapan, pengaturan toko dinamis, dan akun demo:
```bash
php artisan migrate --seed
```

#### Langkah 6: Hubungkan Storage Publik (Untuk Upload Gambar & Logo)
```bash
php artisan storage:link
```

#### Langkah 7: Jalankan Server Lokal
```bash
php artisan serve
```

Aplikasi kini dapat diakses melalui browser di: **[http://localhost:8000](http://localhost:8000)**.

---

## 🔑 Akun Uji Coba Bawaan (Default Credentials)

Database seeder telah menyediakan 4 akun siap pakai untuk seluruh skenario pengujian:

| Role / Peran | Alamat Email | Kata Sandi | Halaman Akses |
| :--- | :--- | :--- | :--- |
| 👑 **Super Admin** | `admin@ngemilkukis.com` | `password` | [`/admin`](http://localhost:8000/admin) (Semua Fitur Terbuka) |
| 🛍️ **Penjual (Seller / Dapur)** | `penjual@ngemilkukis.com` | `password123` | [`/admin`](http://localhost:8000/admin) (Katalog, Stok, Pesanan) |
| 🎧 **Customer Service** | `cs@ngemilkukis.com` | `password123` | [`/admin`](http://localhost:8000/admin) (Monitoring Pesanan) |
| 👤 **Pelanggan VIP** | `customer@ngemilkukis.com` | `password123` | [`/`](http://localhost:8000) (Storefront & Diskon Rp 10rb) |

---

## 🧪 Pengujian Otomatis & Standar Kode

### Menjalankan Test Suite (PHPUnit)
Untuk memastikan seluruh logika keranjang, checkout, diskon member, RBAC, dan audit trail berfungsi tanpa error:
```bash
php vendor/bin/phpunit
```
*Hasil pengujian saat ini: **51 tests passed, 230 assertions, 0 failures**.*

### Memformat Kode (Laravel Pint)
Projek ini mengikuti standar format kode resmi Laravel:
```bash
vendor/bin/pint --format agent
```

---

## 📁 Struktur Direktori Penting

```text
NgemilKukis/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Controller Panel Staf (Dashboard, Orders, Products, Settings, Users, Logs)
│   │   │   ├── Auth/              # Controller Autentikasi Member (Email, Google, Phone OTP)
│   │   │   └── Webhooks/          # Controller Webhook Pembayaran Xendit
│   │   ├── Middleware/            # Middleware Proteksi (EnsureUserIsStaff, EnsureUserRole, Admin)
│   │   └── Requests/              # Form Request Validation dengan otorisasi berbasis Role
│   ├── Models/                    # Eloquent Models (User, Product, Order, OrderItem, ActivityLog, dll.)
│   └── Services/                  # Business Logic (OrderService, ActivityLogger, XenditPaymentService)
├── database/
│   ├── migrations/                # Struktur database skema
│   └── seeders/                   # Seeder data bawaan (produk, stok, kartu ucapan, akun staf)
├── resources/
│   ├── views/
│   │   ├── admin/                 # Blade views untuk panel kontrol (dashboard, users, activity logs, settings)
│   │   ├── shop/                  # Blade views storefront (katalog, checkout, riwayat pesanan)
│   │   ├── components/            # Reusable Blade components (brand-logo, modal, card)
│   │   └── layouts/               # Template layout utama (app.blade.php & admin.blade.php)
│   └── css/                       # Konfigurasi Tailwind CSS v4 & custom tokens
├── routes/
│   └── web.php                    # Definisi rute web publik, member, dan admin domain
└── tests/
    └── Feature/                   # Feature tests lengkap (AdminPanelTest, StaffRoleAndActivityLogTest, dll.)
```

---

## 📄 Lisensi

Projek **NgemilKukis** dikembangkan di bawah lisensi terbuka [MIT License](LICENSE).
Bebas digunakan, dimodifikasi, dan dikembangkan untuk keperluan komersial maupun edukasi.

---

<p align="center">
  Dibuat dengan ❤️ dan aroma wangi kukis cokelat artisanal hangat.
</p>
