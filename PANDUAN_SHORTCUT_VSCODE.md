# 🚀 Panduan Lengkap Shortcut & Snippets VS Code (Global)
> Shortcut ini **sudah terpasang dan aktif di seluruh VS Code Anda**. Mencakup seluruh jenis soal / studi kasus UKK/Serkom Laravel (Pemesanan Makanan, Perpustakaan/Peminjaman Buku, Manajemen User & Role, Laundry, Rental Mobil, Reservasi Lapangan Futsal).

---

## ⚡ Cara Pakai di VS Code
1. Buka file `.blade.php` atau `.php` apa pun di VS Code.
2. Ketik awalan **`sk-`** (contoh: `sk-user-index`, `sk-perpus-peminjaman-controller`, `sk-rental-migration`).
3. Tekan **`Tab`** atau **`Enter`**. Kode langsung ter-generate secara otomatis!
4. Gunakan tombol **`Tab`** untuk berpindah antar placeholder (misal untuk mengubah nama model/variabel).

---

## 📋 Daftar Shortcut Berdasarkan Kategori & Proyek

### 1. Manajemen User, Registrasi & Hak Akses (Role Admin vs User)
*Fitur autentikasi Breeze yang diperluas dengan role & proteksi hak akses:*

| Prefix Shortcut | Lingkup | Keterangan & Komponen yang Dibuat |
| :--- | :--- | :--- |
| `sk-mig-user-role` | Migration | Menambahkan kolom `role` (enum: `admin`, `user` default `user`) pada migrasi `users`. |
| `sk-middleware-role`| PHP Middleware | Kode file `RoleMiddleware` untuk membatasi route berdasarkan role (`admin`/`user`). |
| `sk-auth-redirect` | Controller | Logika pengalihan otomatis login di `AuthenticatedSessionController` (Admin ke `/admin/dashboard`, User ke `/dashboard`). |
| `sk-auth-register-role`| Controller | Penambahan default role `user` saat registrasi di `RegisteredUserController`. |
| `sk-nav-role` | Blade | Kondisional navigasi menu Breeze (`@if(auth()->user()->role === 'admin') ... @else ... @endif`). |
| `sk-user-controller`| Controller | Controller lengkap `Admin\UserController` (CRUD Anggota, Hash Password, Cegah Hapus Diri Sendiri). |
| `sk-user-index` | Blade | Halaman `admin/user/index.blade.php` (Tabel User & Admin, Badge Role Ungu/Biru, Tombol Aksi). |
| `sk-user-create` | Blade | Halaman `admin/user/create.blade.php` (Form Tambah Anggota + Pilihan Role). |
| `sk-user-edit` | Blade | Halaman `admin/user/edit.blade.php` (Form Edit Anggota + Password Baru Opsional). |

---

### 2. Proyek Perpustakaan & Peminjaman Buku (Admin & Siswa)
*Sesuai dokumen `CRUD Transaksi Peminjaman oleh Admin.pdf` & `Siswa katalog dan pinjam.docx`:*

| Prefix Shortcut | File Target | Keterangan |
| :--- | :--- | :--- |
| `sk-perpus-peminjaman-controller` | `Admin\PeminjamanController.php` | Controller transaksi peminjaman admin (Otomatis kurangi stok saat dipinjam, tambah stok saat dikembalikan/dihapus). |
| `sk-perpus-peminjaman-index` | `admin/peminjaman/index.blade.php` | Tabel peminjaman buku admin + badge status warna + tombol Kembalikan & Hapus. |
| `sk-perpus-peminjaman-create` | `admin/peminjaman/create.blade.php` | Form catat peminjaman admin (Dropdown user/siswa, dropdown buku dengan stok > 0, tgl pinjam & kembali). |
| `sk-perpus-katalog-controller` | `User\KatalogController.php` | Controller katalog siswa & peminjaman mandiri 7 hari + riwayat peminjaman siswa login. |
| `sk-perpus-user-katalog` | `user/katalog.blade.php` atau `dashboard.blade.php` | Halaman siswa: Katalog buku tersedia (tombol Pinjam) + Riwayat peminjaman siswa (tombol Kembalikan mandiri). |

---

### 3. Proyek Layanan Laundry / Cuci Kilat
*Sesuai spesifikasi `Sistem Pendaftaran Layanan Laundry.docx`:*

| Prefix Shortcut | Lingkup | Keterangan |
| :--- | :--- | :--- |
| `sk-laundry-migration` | Migration | 3 Skema tabel: `services` (Kiloan/Satuan/Express), `orders` (WA & Status Antrean/Dicuci/Selesai), `order_details`. |
| `sk-laundry-controller-order`| Controller | Controller checkout laundry (`DB::beginTransaction`, hitung berat/qty `price_per_unit * weightOrQty`). |

---

### 4. Proyek Rental Mobil / Kendaraan
*Sesuai spesifikasi `Sistem Rental Mobil.docx`:*

| Prefix Shortcut | Lingkup | Keterangan |
| :--- | :--- | :--- |
| `sk-rental-migration` | Migration | 3 Skema tabel: `cars` (Sedan/SUV/MPV, plat nomor, gambar), `rentals` (NIK, start_date, end_date, Disewa/Selesai/Batal), `rental_details`. |
| `sk-rental-controller-order` | Controller | Controller rental (`DB::beginTransaction`, otomatis hitung selisih hari sewa `diffInDays`, subtotal tarif/hari * hari). |

---

### 5. Proyek Reservasi Lapangan Futsal / Olahraga
*Sesuai spesifikasi `Sistem Reservasi Lapangan Futsal 2.docx`:*

| Prefix Shortcut | Lingkup | Keterangan |
| :--- | :--- | :--- |
| `sk-lapangan-migration` | Migration | 3 Skema tabel: `courts` (Futsal/Badminton/Basket, tarif/jam), `bookings` (No HP, tgl booking, Pending/Lunas/Batal), `booking_details`. |
| `sk-lapangan-controller-order`| Controller | Controller booking lapangan (`DB::beginTransaction`, jam mulai, hitung tarif/jam * durasi jam). |

---

### 6. Proyek Pemesanan Makanan (Modul 1 – 4 Revisi)
| Prefix Shortcut | File Target | Keterangan |
| :--- | :--- | :--- |
| `sk-food-index` | `admin/foods/index.blade.php` | Tabel master makanan + upload gambar + rupiah + edit & hapus. |
| `sk-food-create` | `admin/foods/create.blade.php` | Form input multipart master makanan. |
| `sk-food-edit` | `admin/foods/edit.blade.php` | Form edit master makanan (`@method('PUT')`). |
| `sk-food-customer`| `customer/index.blade.php` | Full page katalog pelanggan Tailwind + filter kategori JS + modal konfirmasi JS. |
| `sk-food-dashboard`| `dashboard.blade.php` | Dashboard rekap pesanan admin + rincian item + inline select PATCH update status. |
| `sk-food-controller`| `FoodController.php` | Controller CRUD master makanan dengan upload/delete gambar. |
| `sk-order-controller`| `OrderController.php` | Controller checkout pesanan makanan multi-item (`DB::beginTransaction`). |
| `sk-food-routes` | `routes/web.php` | Seluruh route aplikasi makanan (publik + grup admin). |

---

### 7. Komponen Cepat (Micro-Snippets)
| Prefix Shortcut | Lingkup | Fungsi |
| :--- | :--- | :--- |
| `sk-alert` | Blade | Notifikasi session success, error, dan validasi `$errors->any()`. |
| `sk-rupiah` | Blade / PHP | Format rupiah: `Rp {{ number_format($item->price) }}` |
| `sk-badge-status` | Blade | Badge status warna serbaguna (Pending, Antrean, Dipinjam, Disewa -> Kuning; Selesai, Lunas, Dikembalikan -> Hijau). |
| `sk-form-delete` | Blade | Tombol hapus inline dengan confirm popup dan `@method('DELETE')`. |
| `sk-stok-decrement`| PHP | Cek ketersediaan stok lalu `decrement('stok')`. |
| `sk-stok-increment`| PHP | Kembalikan stok saat barang/buku dikembalikan (`increment('stok')`). |
| `sk-calc-days` | PHP | Hitung selisih hari sewa menggunakan `Carbon::parse()->diffInDays()`. |
| `sk-gen-index` | Blade | Template tabel CRUD index generic dengan placeholder tabstop. |
| `sk-gen-create` | Blade | Template form tambah data generic dengan upload file. |
| `sk-gen-edit` | Blade | Template form edit data generic. |
