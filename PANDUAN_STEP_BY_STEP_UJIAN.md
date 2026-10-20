# 📘 BUKU PANDUAN MASTER: STEP-BY-STEP PENGERJAAN SERKOM / UKK LARAVEL
> **Target:** Menyelesaikan aplikasi Laravel dari nol hingga 100% jadi dan bebas bug dalam waktu kurang dari 30–45 menit dengan kolaborasi seluruh **Shortcut Snippets VS Code (`sk-...`)**.

---

## 🧭 PETA KONSEP: 2 KELOMPOK BESAR JENIS SOAL

Dalam ujian sertifikasi/UKK berbasis Laravel, seluruh jenis soal sebenarnya hanya terbagi menjadi **2 Pola Arsitektur Utama**:

```
                                 JENIS SOAL UJIAN
                                        │
         ┌──────────────────────────────┴──────────────────────────────┐
         ▼                                                             ▼
┌─────────────────────────────────┐           ┌─────────────────────────────────┐
│     POLA A: TRANSAKSI ORDER     │           │    POLA B: MULTI-ROLE & PINJAM  │
│  (Katalog Publik & Admin Rekap) │           │    (Admin CRUD vs Siswa Mandiri)│
├─────────────────────────────────┤           ├─────────────────────────────────┤
│ 1. Pemesanan Makanan (Restoran) │           │ 1. Peminjaman Buku Perpustakaan │
│ 2. Laundry / Cuci Kilat         │           │ 2. Inventaris Alat Lab / Kantor │
│ 3. Rental Mobil / Kendaraan     │           │ 3. Manajemen User (Admin/User)  │
│ 4. Reservasi Lapangan Futsal    │           │                                 │
└─────────────────────────────────┘           └─────────────────────────────────┘
```

* **Pola A (Pemesanan/Sewa/Layanan):** Memiliki 3 tabel (`Master`, `Orders`, `Order Details`), pelanggan memilih di katalog tanpa wajib login atau mengisi form di depan, menggunakan `DB::beginTransaction()`, dan admin mengelola master data serta mengupdate status transaksi.
* **Pola B (Perpustakaan/Peminjaman/Role):** Memiliki tabel `users` dengan kolom `role`, tabel master stok, dan tabel peminjaman. Memiliki logika penambahan/pengurangan stok otomatis (`decrement` saat dipinjam, `increment` saat dikembalikan).

---

# 🍔 BAGIAN 1: STEP-BY-STEP SOAL POLA A (STUDI KASUS: PEMESANAN MAKANAN)
*(Untuk Laundry, Rental Mobil, dan Lapangan Futsal cara kerjanya **100% SAMA**, Anda hanya perlu mengganti nama tabel dan kolom sesuai [Tabel Transformasi](#-tabel-transformasi-pola-a-laundry-rental-mobil-futsal)).*

---

### Langkah 1: Persiapan Project & Konfigurasi Database
1. Buka XAMPP, pastikan modul **Apache** dan **MySQL** berstatus `Running`.
2. Buka Command Prompt / Terminal, buat database baru di MySQL atau lewat phpMyAdmin (misal: `db_pemesanan_makanan`).
3. Buka file `.env` di VS Code, ubah konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_pemesanan_makanan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

---

### Langkah 2: Generate Model, Migration, Controller & Seeder Sekaligus
Buka terminal VS Code (`Ctrl + ~`), jalankan 4 baris perintah ini:
```bash
php artisan make:model Food -mcr
php artisan make:model Order -mcr
php artisan make:model OrderDetail -m
php artisan make:seeder FoodSeeder
```
> [!NOTE]
> * Flag `-mcr` otomatis membuatkan file **Model**, **Migration**, dan **Controller Resource**.
> * Flag `-m` membuatkan file **Model** dan **Migration**.

---

### Langkah 3: Mengisi Skema Migration (Struktur Tabel)

#### 1. File: `database/migrations/xxxx_create_food_table.php`
* **PENTING:** Pastikan nama tabel pada `Schema::create` adalah `'foods'` (berakhiran `s`).
* Di dalam fungsi `up()`, hapus isi dalamnya lalu ketik:
  👉 **`sk-food-mig-foods`** lalu tekan **`Tab`**.
* *Hasilnya:*
  ```php
  Schema::create('foods', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->enum('category', ['Makanan', 'Minuman', 'Cemilan']);
      $table->integer('price');
      $table->text('description');
      $table->string('image')->nullable();
      $table->timestamps();
  });
  ```

#### 2. File: `database/migrations/xxxx_create_orders_table.php`
* Di dalam fungsi `up()`, ketik:
  👉 **`sk-food-mig-orders`** lalu tekan **`Tab`**.
* *Hasilnya:*
  ```php
  Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->string('customer_name');
      $table->string('table_number');
      $table->integer('total_price');
      $table->string('status')->default('Pending');
      $table->timestamps();
  });
  ```

#### 3. File: `database/migrations/xxxx_create_order_details_table.php`
* Di dalam fungsi `up()`, ketik:
  👉 **`sk-food-mig-details`** lalu tekan **`Tab`**.
* *Hasilnya:*
  ```php
  Schema::create('order_details', function (Blueprint $table) {
      $table->id();
      $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
      $table->foreignId('food_id')->constrained('foods')->onDelete('cascade');
      $table->integer('quantity');
      $table->integer('subtotal');
      $table->timestamps();
  });
  ```

---

### Langkah 4: Mengisi Seeder Data Awal
Penguji wajib melihat data awal tanpa harus menginput manual dari form.

#### 1. File: `database/seeders/FoodSeeder.php`
* Di dalam method `run()`, ketik:
  👉 **`sk-food-seeder`** lalu tekan **`Tab`**.
  *(Otomatis terisi 5 menu dummy: Nasi Goreng, Es Teh, Keripik Singkong, Mie Ayam, Jus Jeruk)*.

#### 2. File: `database/seeders/DatabaseSeeder.php`
* Buka method `run()`, daftarkan `FoodSeeder`:
  ```php
  public function run(): void
  {
      $this->call([
          FoodSeeder::class,
      ]);
  }
  ```

#### 3. Eksekusi Migrasi di Terminal:
Jalankan perintah:
```bash
php artisan migrate:fresh --seed
```
*(Pastikan semua status tabel bertuliskan `DONE`)*.

---

### Langkah 5: Setup Relasi pada Eloquent Model

#### 1. File: `app/Models/Food.php`
Tambahkan proteksi guarded:
```php
protected $table = 'foods';
protected $guarded = ['id'];
```

#### 2. File: `app/Models/Order.php`
Order memiliki banyak rincian:
```php
protected $table = 'orders';
protected $guarded = ['id'];

public function orderDetails()
{
    return $this->hasMany(OrderDetail::class);
}
```

#### 3. File: `app/Models/OrderDetail.php`
Rincian terhubung ke Order dan Food:
```php
protected $table = 'order_details';
protected $guarded = ['id'];

public function order()
{
    return $this->belongsTo(Order::class);
}

public function food()
{
    return $this->belongsTo(Food::class);
}
```

---

### Langkah 6: Instalasi Autentikasi Admin (Laravel Breeze)
Jalankan di terminal secara berurutan:
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
# (Pilih Blade -> Dark Mode: No -> Test runner: PHPUnit)
php artisan migrate
php artisan storage:link
npm install
npm run build
```
> [!IMPORTANT]
> Jangan pernah lupa menjalankan `php artisan storage:link`! Jika terlewat, foto yang diupload di admin tidak akan pernah bisa muncul di browser.

---

### Langkah 7: Mengisi Logika Controller (Dengan Komentar Baris per Baris)

#### 1. File: `app/Http/Controllers/FoodController.php`
* Blok semua isi file (`Ctrl + A`), lalu ketik:
  👉 **`sk-food-controller`** lalu tekan **`Tab`**.
* **Apa yang dihasilkan?**
  * Method `index()` dengan pagination 10 data.
  * Method `create()` membuka form.
  * Method `store()` validasi gambar, upload ke folder `storage/public/foods`, simpan record.
  * Method `edit()` & `update()` mengganti gambar dan menghapus file lama dari storage jika diganti.
  * Method `destroy()` menghapus data dan file gambar terkait di disk.
  * Dilengkapi baris komentar penjelasan di setiap baris kodenya.

#### 2. File: `app/Http/Controllers/OrderController.php`
* Blok semua isi file (`Ctrl + A`), lalu ketik:
  👉 **`sk-order-controller`** lalu tekan **`Tab`**.
* **Apa yang dihasilkan?**
  * Method `index()` menampilkan katalog menu makanan.
  * Method `store()` menjalankan `DB::beginTransaction()`, looping subtotal item yang dipesan, commit transaksi, dan rollback jika error.
  * Method `adminDashboard()` mengambil pesanan beserta eager loading `orderDetails.food`.
  * Method `updateStatus()` mengupdate status pesanan (Pending/Completed/Cancelled).

---

### Langkah 8: Konfigurasi Routing Web

#### File: `routes/web.php`
* Buka file `routes/web.php`, blok semua kodenya lalu ketik:
  👉 **`sk-food-routes`** lalu tekan **`Tab`**.
* **Alur Rute yang Terbentuk:**
  * Halaman publik pelanggan: `GET /` (katalog) dan `POST /checkout` (proses pesan).
  * Halaman admin login: `/dashboard` (rekap pesanan), `/admin/orders/{id}/status` (ubah status), dan `/admin/foods` (CRUD master makanan).

---

### Langkah 9: Membuat Seluruh Tampilan Blade

#### A. CRUD Master Makanan (Admin)
Buat folder: `resources/views/admin/foods/`
1. Buat file `index.blade.php`:
   * Ketik: 👉 **`sk-food-index`** lalu tekan **`Tab`**.
   * *(Otomatis jadi tabel responsif Breeze, format rupiah, preview foto, tombol edit, form hapus popup confirm, dan pagination)*.
2. Buat file `create.blade.php`:
   * Ketik: 👉 **`sk-food-create`** lalu tekan **`Tab`**.
   * *(Otomatis jadi form input multipart, select kategori enum, input rupiah, upload gambar)*.
3. Buat file `edit.blade.php`:
   * Ketik: 👉 **`sk-food-edit`** lalu tekan **`Tab`**.
   * *(Otomatis jadi form edit dengan `@method('PUT')`, prefill value lama, dan auto-selected kategori)*.

#### B. Dashboard Rekap Pesanan (Admin)
Buka file: `resources/views/dashboard.blade.php`
* Ganti seluruh kodenya dengan mengetik:
  👉 **`sk-food-dashboard`** lalu tekan **`Tab`**.
* *(Otomatis jadi rekap pesanan, rincian item, badge warna status otomatis, dan form select inline PATCH status)*.

#### C. Katalog Menu & Checkout (Sisi Pelanggan Publik)
Buat folder & file: `resources/views/customer/index.blade.php`
* Ketik: 👉 **`sk-food-customer`** lalu tekan **`Tab`**.
* *(Otomatis jadi tampilan modern Tailwind CDN, filter menu instan via JavaScript, input kuantitas porsi, dan popup modal konfirmasi ringkasan pesanan sebelum submit)*.

---

# 🔄 TABEL TRANSFORMASI POLA A (LAUNDRY, RENTAL MOBIL, FUTSAL)
Jika Anda mendapatkan soal Laundry, Rental Mobil, atau Reservasi Futsal, **langkah 1 sampai 9 di atas persis sama**, Anda cukup mengganti nama sesuai tabel berikut:

| Komponen | 🍔 Pemesanan Makanan | 🧺 Laundry Kilat | 🚗 Rental Mobil | ⚽ Reservasi Lapangan |
| :--- | :--- | :--- | :--- | :--- |
| **Model Master** | `Food` | `Service` | `Car` | `Court` |
| **Model Transaksi** | `Order` | `Order` | `Rental` | `Booking` |
| **Model Detail** | `OrderDetail` | `OrderDetail` | `RentalDetail` | `BookingDetail` |
| **Shortcut Migrasi**| `sk-food-mig-...` | `sk-laundry-migration` | `sk-rental-migration` | `sk-lapangan-migration`|
| **Shortcut Order** | `sk-order-controller`| `sk-laundry-controller-order`| `sk-rental-controller-order`| `sk-lapangan-controller-order`|
| **Field Khusus** | `table_number` | `whatsapp_number` | `nik_number`, `start_date` | `customer_phone`, `booking_date` |
| **Satuan Unit** | Porsi / Pcs | Kg / Pcs | Hari (`diffInDays`) | Jam (`duration_hours`) |
| **Status Transaksi**| Pending / Selesai | Antrean / Dicuci / Selesai | Disewa / Selesai / Batal | Pending / Lunas / Batal |
| **Shortcut Tampilan**| `sk-food-...` | `sk-gen-index`, `sk-gen-create` | `sk-gen-index`, `sk-gen-create` | `sk-gen-index`, `sk-gen-create` |

---

# 📚 BAGIAN 2: STEP-BY-STEP SOAL POLA B (PERPUSTAKAAN, ANGGOTA & ROLE)
*(Mengacu pada `CRUD Transaksi Peminjaman oleh Admin.pdf`, `admin-user (1).docx`, dan `Siswa katalog dan pinjam.docx`)*.

---

### Langkah 1: Struktur Database & Kolom Role User
1. Buka file migration user: `database/migrations/0001_01_01_000000_create_users_table.php`.
2. Di dalam definisi kolom, tambahkan kolom `role`:
   Ketik: 👉 **`sk-mig-user-role`** lalu tekan **`Tab`**.
   ```php
   $table->enum('role', ['admin', 'user'])->default('user')->after('password');
   ```
3. Buat migration buku dan peminjaman di terminal:
   ```bash
   php artisan make:model Buku -m
   php artisan make:model Peminjaman -m
   ```
4. Isi migration `bukus`:
   ```php
   Schema::create('bukus', function (Blueprint $table) {
       $table->id();
       $table->string('kode_buku')->unique();
       $table->string('judul');
       $table->string('pengarang');
       $table->string('penerbit');
       $table->integer('stok')->default(0);
       $table->timestamps();
   });
   ```
5. Isi migration `peminjamans`:
   ```php
   Schema::create('peminjamans', function (Blueprint $table) {
       $table->id();
       $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
       $table->foreignId('buku_id')->constrained('bukus')->onDelete('cascade');
       $table->date('tanggal_pinjam');
       $table->date('tanggal_kembali');
       $table->enum('status', ['dipinjam', 'dikembalikan'])->default('dipinjam');
       $table->timestamps();
   });
   ```
6. Jalankan: `php artisan migrate`.

---

### Langkah 2: Menyiapkan Akun Default di Seeder
Agar langsung bisa login sebagai Admin dan Siswa tanpa registrasi manual:
Buka `database/seeders/DatabaseSeeder.php`:
```php
public function run(): void
{
    // Akun Admin
    \App\Models\User::create([
        'name' => 'Administrator',
        'email' => 'admin@gmail.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    // Akun Siswa/User
    \App\Models\User::create([
        'name' => 'Siswa Test',
        'email' => 'siswa@gmail.com',
        'password' => bcrypt('password'),
        'role' => 'user',
    ]);
}
```
Jalankan: `php artisan migrate:fresh --seed`.

---

### Langkah 3: Setup Model & Relasi
1. **`app/Models/User.php`**:
   Tambahkan `'role'` pada `$fillable` dan relasi peminjaman:
   ```php
   protected $fillable = ['name', 'email', 'password', 'role'];

   public function peminjamans()
   {
       return $this->hasMany(Peminjaman::class);
   }
   ```
2. **`app/Models/Peminjaman.php`**:
   ```php
   protected $table = 'peminjamans';
   protected $guarded = ['id'];

   public function user() { return $this->belongsTo(User::class); }
   public function buku() { return $this->belongsTo(Buku::class); }
   ```

---

### Langkah 4: Logika Login & Redirect Berdasarkan Role
Buka file Breeze: `app/Http/Controllers/Auth/AuthenticatedSessionController.php`:
Cari method `store()`, sesuaikan redirect setelah `$request->authenticate()`:
Ketik: 👉 **`sk-auth-redirect`** lalu tekan **`Tab`**.
```php
if (auth()->user()->role === 'admin') {
    return redirect()->intended(route('admin.dashboard'));
}
return redirect()->intended(route('dashboard'));
```

---

### Langkah 5: Controller Sisi Admin (Kelola User & Peminjaman)

1. Buat controller user di terminal:
   `php artisan make:controller Admin/UserController`
   * Buka file: `app/Http/Controllers/Admin/UserController.php`
   * Blok seluruh kode, ketik: 👉 **`sk-user-controller`** lalu tekan **`Tab`**.
   * *(Otomatis jadi CRUD User, enkripsi password `Hash::make`, proteksi akun sendiri)*.

2. Buat controller peminjaman admin:
   `php artisan make:controller Admin/PeminjamanController`
   * Buka file: `app/Http/Controllers/Admin/PeminjamanController.php`
   * Blok seluruh kode, ketik: 👉 **`sk-perpus-peminjaman-controller`** lalu tekan **`Tab`**.
   * *(Otomatis ada logika `decrement('stok')` saat pinjam dan `increment('stok')` saat kembalikan)*.

---

### Langkah 6: Controller Sisi Siswa (Katalog & Pinjam Mandiri)
1. Buat controller siswa di terminal:
   `php artisan make:controller User/KatalogController`
2. Buka file: `app/Http/Controllers/User/KatalogController.php`
   * Blok seluruh kode, ketik: 👉 **`sk-perpus-katalog-controller`** lalu tekan **`Tab`**.
   * *(Menampilkan buku yang stoknya > 0 dan riwayat pinjam siswa login)*.

---

### Langkah 7: Tampilan Blade Sisi Admin & Siswa

1. **Admin - Kelola User (`resources/views/admin/user/`)**:
   * `index.blade.php` : Ketik 👉 **`sk-user-index`** + **`Tab`**.
   * `create.blade.php`: Ketik 👉 **`sk-user-create`** + **`Tab`**.
   * `edit.blade.php`  : Ketik 👉 **`sk-user-edit`** + **`Tab`**.

2. **Admin - Kelola Peminjaman (`resources/views/admin/peminjaman/`)**:
   * `index.blade.php` : Ketik 👉 **`sk-perpus-peminjaman-index`** + **`Tab`**.
   * `create.blade.php`: Ketik 👉 **`sk-perpus-peminjaman-create`** + **`Tab`**.

3. **Siswa - Katalog & Riwayat (`resources/views/dashboard.blade.php` atau `user/katalog.blade.php`)**:
   * Buka file `resources/views/dashboard.blade.php`, blok semua kodenya lalu ketik:
     👉 **`sk-perpus-user-katalog`** + **`Tab`**.

4. **Navigasi Multi-Role (`resources/views/layouts/navigation.blade.php`)**:
   * Pada bagian menu link di navbar, gunakan:
     👉 **`sk-nav-role`** + **`Tab`**.

---

# 🛠️ BAGIAN 3: MICRO-SNIPPET CEPAT UNTUK MODIFIKASI
Jika Anda butuh membuat bagian-bagian kecil tanpa template 1 halaman penuh:

| Kebutuhan Anda | Shortcut | Hasil yang Dibuat |
| :--- | :--- | :--- |
| **Notifikasi Alert** | `sk-alert` | Blok pesan `session('success')`, `session('error')`, dan list `$errors->any()`. |
| **Format Uang** | `sk-rupiah` | `Rp {{ number_format($item->price) }}` |
| **Warna Status** | `sk-badge-status`| Badge Tailwind otomatis (Kuning: Pending/Antrean/Dipinjam; Hijau: Selesai/Lunas/Dikembalikan). |
| **Tombol Hapus** | `sk-form-delete` | Form inline method DELETE dengan konfirmasi javascript `confirm()`. |
| **Input Text** | `sk-input-text` | Form group label + input teks Tailwind. |
| **Input Angka** | `sk-input-number` | Form group label + input angka Tailwind. |
| **Input Dropdown**| `sk-input-select` | Dropdown Tailwind dengan seleksi nilai lama (`old()`). |
| **Upload Gambar** | `sk-input-file` | Form upload file gambar Tailwind. |
| **Kurangi Stok** | `sk-stok-decrement`| Pengecekan stok kosong dan `$item->decrement('stok')`. |
| **Tambah Stok** | `sk-stok-increment`| `$item->increment('stok')` saat barang/buku kembali. |
| **Hitung Hari** | `sk-calc-days` | Perhitungan selisih hari sewa `diffInDays()` menggunakan Carbon. |

---

# ⚠️ BAGIAN 4: 7 JEBAKAN ERROR PALING SERING DI UJIAN (WAJIB INGAT!)

1. **Gambar Tidak Muncul di Browser:**
   * *Penyebab:* Lupa menjalankan link storage.
   * *Solusi:* Jalankan `php artisan storage:link` di terminal.
2. **File Gambar Tidak Masuk ke Controller:**
   * *Penyebab:* Lupa menambahkan atribut `enctype="multipart/form-data"` pada tag `<form>`.
   * *Solusi:* Seluruh snippet form tambah/edit (`sk-food-create`, `sk-gen-create`) sudah menyertakan ini secara otomatis.
3. **Foreign Key Error saat Menjalankan `migrate`:**
   * *Penyebab:* Tabel detail dijalankan sebelum tabel induk dibuat, atau nama tabel singular/plural tidak cocok.
   * *Solusi:* Pastikan tabel `orders` / `bukus` dibuat lebih dulu daripada tabel `order_details` / `peminjamans`.
4. **Method Not Allowed (405):**
   * *Penyebab:* Form edit memakai method POST tanpa `@method('PUT')`, atau form hapus tanpa `@method('DELETE')`.
5. **Page Expired (419):**
   * *Penyebab:* Lupa menyertakan `@csrf` di dalam tag `<form>`.
6. **Integrity Constraint Violation (Cannot delete or update a parent row):**
   * *Penyebab:* Menghapus data induk yang masih memiliki anak di detail.
   * *Solusi:* Gunakan `->onDelete('cascade')` pada migration foreignId (seperti yang sudah ada di snippet `sk-food-mig-details` dan `sk-mig-foreign`).
7. **Error Akses Akun Terhapus Sendiri:**
   * *Solusi:* Di controller User, selalu sertakan proteksi `if ($user->id === auth()->id())` agar admin tidak sengaja menghapus akun yang sedang dipakai ujian.

---

# ⏱️ BAGIAN 5: TIMELINE PENGERJAAN UJIAN (TARGET 35 MENIT)

| Menit Ke- | Aktivitas | Shortcut yang Dipakai |
| :---: | :--- | :--- |
| **00 – 05** | Nyalakan XAMPP, buat database, edit `.env`, install Breeze & storage link. | Terminal |
| **05 – 10** | Buat Model, Migration, Seeder, dan isi skema database. | `sk-...-mig-...`, `sk-...-seeder` |
| **10 – 15** | Jalankan `migrate:fresh --seed`, atur relasi Model `$guarded` & `hasMany`. | `sk-model-hasmany`, `sk-model-belongsto` |
| **15 – 22** | Isi Controller CRUD & Transaksi (langsung lengkap dengan komentar per baris). | `sk-...-controller` |
| **22 – 25** | Atur `routes/web.php` (rute publik & grup admin). | `sk-food-routes` / route snippets |
| **25 – 33** | Buat tampilan Blade (Admin Table, Form Tambah/Edit, Dashboard, & Customer). | `sk-...-index`, `sk-...-create`, `sk-...-customer` |
| **33 – 35** | Uji coba demo aplikasi: Tambah menu, upload foto, lakukan checkout, cek dashboard admin, ubah status pesanan. Selesai! | Browser |
