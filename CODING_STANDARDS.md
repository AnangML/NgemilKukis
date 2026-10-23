# NgemilKukis — Enterprise Coding Standards & Architecture Guidelines

Dokumen ini mendefinisikan standar rekayasa perangkat lunak, konvensi arsitektur, dan aturan penulisan kode untuk repositori **NgemilKukis**. Seluruh pengembang dan AI Agent wajib mematuhi panduan ini secara konsisten.

---

## 1. Prinsip Arsitektur Utama (Core Architecture Principles)

### 1.1. Larangan "Universal / God Controller" (No Universal Controllers)
* **Aturan Mutlak:** Dilarang keras membuat satu controller raksasa yang menangani berbagai domain sekaligus (misalnya: menaruh katalog, checkout, proses pesanan, webhook pembayaran, dan simulasi dalam satu `ShopController`).
* **Single Responsibility Principle (SRP):** Setiap controller hanya boleh merepresentasikan satu resource spesifik atau satu context bisnis tunggal.
* **Pembagian Domain Controller:**
  * `CatalogController` atau `HomeController`: Menangani penampilan etalase & filter produk.
  * `CheckoutController`: Menangani inisiasi halaman checkout.
  * `OrderController`: Menangani siklus hidup pesanan (`store`, `show`, `cancel`).
  * `XenditWebhookController`: Khusus menangani webhook incoming payment dari Xendit.
  * `SimulatePaymentController`: Khusus endpoint simulasi pembayaran di lingkungan lokal/testing.

### 1.2. Thin Controllers, Rich Domain / Services
* Controller hanya bertugas sebagai **HTTP Traffic Controller**:
  1. Menerima dan memvalidasi HTTP Request via dedicated `FormRequest`.
  2. Mendelegasikan orkestrasi ke Model / Service / Action.
  3. Mengembalikan HTTP Response (View / JSON / Redirect).
* Tidak boleh ada logika bisnis kompleks, komputasi harga manual yang panjang, atau panggilan API pihak ketiga yang bertumpuk langsung di dalam method controller.

### 1.3. Validasi Terisolasi (Dedicated Form Requests)
* **Larangan:** Hindari validasi inline seperti `$request->validate([...])` di dalam controller untuk mutasi data (`POST`, `PUT`, `PATCH`).
* **Wajib:** Gunakan class Form Request terpisah, misalnya `StoreOrderRequest`, `UpdateProductRequest`.
* Seluruh aturan validasi, pesan kesalahan, dan otorisasi input harus berada di dalam Form Request tersebut.

---

## 2. Standar Penulisan PHP & Laravel (PHP 8.3+)

### 2.1. Strict Typing & Explicit Return Types
* Semua method wajib memiliki deklarasi tipe parameter (*type hints*) dan tipe kembalian (*return type*) yang eksplisit.
* Gunakan fitur modern PHP 8.3 (Constructor Property Promotion, Match expressions, Nullsafe operator).

```php
// CONTOH YANG BENAR
public function show(string $orderNumber): View
{
    $order = $this->orderService->getByNumber($orderNumber);
    return view('orders.show', compact('order'));
}

// CONTOH YANG DILARANG (Tanpa return type dan tanpa type hint)
public function show($orderNumber)
{
    $order = Order::where('order_number', $orderNumber)->first();
    return view('orders.show', ['order' => $order]);
}
```

### 2.2. Pemformatan Kode Otomatis (Laravel Pint)
* Kode harus selalu mematuhi standar PSR-12 / Laravel Preset.
* Sebelum menyelesaikan perubahan pada file PHP, jalankan:
  ```bash
  vendor/bin/pint --format agent
  ```

### 2.3. Keamanan Transaksi Database
* Operasi multitabel wajib dibungkus dalam `DB::transaction()` untuk menjamin integritas data (ACID).
* **Zero Trust Client Input:** Jangan pernah mempercayai harga produk atau total belanja yang dikirimkan oleh browser/klien JavaScript. Harga dan subtotal wajib dihitung ulang dari database server.

---

## 3. Konvensi Struktur Direktori & File

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── CatalogController.php        # Tampilan katalog & beranda
│   │   ├── CheckoutController.php       # Inisialisasi proses checkout
│   │   ├── OrderController.php          # Pembuatan & rincian pesanan
│   │   ├── Webhooks/
│   │   │   └── XenditWebhookController.php # Webhook handler Xendit
│   │   └── Testing/
│   │       └── SimulatePaymentController.php # Mock payment simulation
│   └── Requests/
│       └── StoreOrderRequest.php        # Validasi pembuatan pesanan
├── Models/                              # Eloquent Models dengan $fillable & casts
│   ├── Product.php
│   ├── GreetingCardTemplate.php
│   ├── Order.php
│   └── OrderItem.php
└── Services/                            # Business logic & payment integrations
    ├── OrderService.php
    └── XenditPaymentService.php
```

---

## 4. Standar Desain Antarmuka (UI/UX 60-30-10)

Untuk menjaga konsistensi visual brand **NgemilKukis**, seluruh antarmuka web wajib mengikuti aturan warna dan tipografi berikut:

### 4.1. Formula Warna 60-30-10
* **60% Dominan — Putih Bersih (`#FFFFFF`):**
  * Digunakan untuk kanvas latar belakang (`body`), kartu produk, formulir, dan ruang bernapas (*whitespace*).
  * Memberikan kesan bersih, terang, modern, dan higienis.
* **30% Sekunder — Coklat Agak Tua (`#2D180C`):**
  * Digunakan untuk teks judul (*headings*), teks konten utama, batas tipis netral, logo teks, dan footer.
  * Memberikan kontras visual tinggi dan identitas cokelat artisan yang elegan.
* **10% Aksen — Coklat Muda (`#B87D4B` / Warm Caramel):**
  * **Wajib digunakan untuk SEMUA TOMBOL aksi (Call To Action / CTA).**
  * Digunakan untuk bintang rating, badge pilihan aktif, dan sorotan harga.
  * Warna hover tombol: `#A36B3B` (transisi halus).

### 4.2. Tipografi
* **Font Utama:** `Plus Jakarta Sans` untuk seluruh judul, paragraf, tombol, dan formulir.
* Dilarang mencampur font dekoratif yang berlebihan agar tampilan tetap modern, geometris, dan profesional.

---

## 5. Standar Pengujian (Testing Guidelines)

1. **Pola AAA (Arrange, Act, Assert):** Setiap pengujian harus terstruktur dengan jelas dalam mempersiapkan data, mengeksekusi aksi, dan memverifikasi ekspektasi.
2. **Feature Tests Terfokus:**
   * Setiap controller wajib memiliki pengujian feature test pendamping.
   * Uji kasus sukses (*happy path*) dan kasus kegagalan (*validation error, boundary condition*).
3. **Database Isolasi:**
   * Setiap class pengujian wajib menggunakan trait `Illuminate\Foundation\Testing\RefreshDatabase`.
   * Data uji wajib dibuat menggunakan **Model Factories**, bukan manual raw insert.
4. **Perintah Menjalankan Tes:**
   ```bash
   php artisan test --compact
   ```
   Seluruh tes harus lulus 100% sebelum kode dinyatakan siap rilis.

---

## 6. Ringkasan Do's & Don'ts

| Kategori | DO (Lakukan) | DON'T (Dilarang) |
| :--- | :--- | :--- |
| **Controller** | Pisahkan per domain/resource (`CheckoutController`, `OrderController`). | Membuat satu `ShopController` universal untuk semua fitur. |
| **Validasi** | Gunakan dedicated `FormRequest` class. | Validasi inline `$request->validate()` di controller. |
| **Kalkulasi Harga** | Ambil harga resmi dari database tabel `products`. | Memakai `price` atau `subtotal` kiriman klien JavaScript. |
| **Warna UI** | Putih (60%), Coklat Tua (30%), Tombol Coklat Muda (10%). | Menambahkan warna acak di luar palet brand. |
| **Tipografi** | Gunakan `Plus Jakarta Sans` yang clean & modern. | Memakai font serif/cursive yang tidak konsisten. |
| **Formatting** | Jalankan `vendor/bin/pint --format agent` sebelum selesai. | Menyimpan file PHP tanpa formatting standar PSR-12. |
