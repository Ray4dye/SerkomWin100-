<?php

namespace App\Http\Controllers;

// Import Model Food untuk berinteraksi dengan tabel 'foods'
use App\Models\Food;
// Import Request untuk menangani data inputan dari form pengguna
use Illuminate\Http\Request;
// Import Storage untuk operasi file upload/hapus gambar di storage publik
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    /**
     * Menampilkan daftar semua makanan (Halaman Index Admin)
     */
    public function index()
    {
        // Mengambil data makanan terbaru dari database dengan pagination 10 data per halaman
        $foods = Food::latest()->paginate(10);

        // Mengirimkan variabel $foods ke tampilan resources/views/admin/foods/index.blade.php
        return view('admin.foods.index', compact('foods'));
    }

    /**
     * Menampilkan form untuk menambahkan data makanan baru
     */
    public function create()
    {
        // Membuka view form tambah makanan di resources/views/admin/foods/create.blade.php
        return view('admin.foods.create');
    }

    /**
     * Menyimpan data makanan baru ke database (Proses Tambah Data)
     */
    public function store(Request $request)
    {
        // 1. Validasi input: memastikan semua kolom yang wajib sudah diisi sesuai aturan
        $request->validate([
            'name'        => 'required|string|max:255', // Nama wajib diisi, berupa teks, maksimal 255 karakter
            'category'    => 'required|in:Makanan,Minuman,Cemilan', // Kategori wajib dipilih dari 3 opsi ini
            'price'       => 'required|numeric|min:0', // Harga wajib angka dan tidak boleh minus
            'description' => 'required|string', // Deskripsi makanan wajib diisi
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Gambar opsional, format jpg/png, maksimal 2MB
        ]);

        // 2. Menyiapkan variabel penampung path gambar
        $imagePath = null;

        // 3. Cek apakah pengguna mengunggah file gambar pada input form
        if ($request->hasFile('image')) {
            // Simpan file ke folder storage/app/public/foods dan ambil path-nya
            $imagePath = $request->file('image')->store('foods', 'public');
        }

        // 4. Masukkan data makanan baru ke dalam tabel 'foods'
        Food::create([
            'name'        => $request->name,        // Simpan nama makanan
            'category'    => $request->category,    // Simpan kategori makanan
            'price'       => $request->price,       // Simpan harga makanan
            'description' => $request->description, // Simpan deskripsi makanan
            'image'       => $imagePath,            // Simpan path file gambar (atau null jika tanpa gambar)
        ]);

        // 5. Kembalikan pengguna ke halaman index dengan pesan notifikasi sukses
        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit untuk makanan tertentu
     */
    public function edit(Food $food)
    {
        // Membuka view edit dan mengirimkan data makanan yang dipilih ($food)
        return view('admin.foods.edit', compact('food'));
    }

    /**
     * Memperbarui data makanan di database (Proses Update Data)
     */
    public function update(Request $request, Food $food)
    {
        // 1. Validasi data yang diinput pada form edit
        $request->validate([
            'name'        => 'required|string|max:255', // Nama wajib diisi
            'category'    => 'required|in:Makanan,Minuman,Cemilan', // Kategori harus valid
            'price'       => 'required|numeric|min:0', // Harga harus valid
            'description' => 'required|string', // Deskripsi wajib diisi
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Gambar opsional
        ]);

        // 2. Gunakan gambar yang lama sebagai nilai default jika tidak ada gambar baru
        $imagePath = $food->image;

        // 3. Cek jika pengguna memilih file gambar baru
        if ($request->hasFile('image')) {
            // Hapus gambar lama dari storage jika file fisiknya masih ada
            if ($food->image && Storage::disk('public')->exists($food->image)) {
                Storage::disk('public')->delete($food->image);
            }
            // Simpan gambar baru ke folder storage/app/public/foods
            $imagePath = $request->file('image')->store('foods', 'public');
        }

        // 4. Perbarui data makanan pada database
        $food->update([
            'name'        => $request->name,        // Update nama makanan
            'category'    => $request->category,    // Update kategori
            'price'       => $request->price,       // Update harga
            'description' => $request->description, // Update deskripsi
            'image'       => $imagePath,            // Update gambar (baru atau tetap yang lama)
        ]);

        // 5. Alihkan kembali ke halaman daftar makanan dengan pesan sukses
        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil diperbarui!');
    }

    /**
     * Menghapus data makanan dari database
     */
    public function destroy(Food $food)
    {
        // 1. Cek apakah ada file gambar di storage, jika ada maka hapus file gambarnya
        if ($food->image && Storage::disk('public')->exists($food->image)) {
            Storage::disk('public')->delete($food->image);
        }

        // 2. Hapus baris data makanan dari tabel database
        $food->delete();

        // 3. Alihkan kembali ke halaman index dengan pesan konfirmasi berhasil dihapus
        return redirect()->route('foods.index')->with('success', 'Data makanan berhasil dihapus!');
    }
}
