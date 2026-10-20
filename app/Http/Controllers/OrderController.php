<?php

namespace App\Http\Controllers;

// Import model Food, Order, dan OrderDetail untuk transaksi pemesanan
use App\Models\Food;
use App\Models\Order;
use App\Models\OrderDetail;
// Import Request untuk mengambil input formulir checkout pelanggan
use Illuminate\Http\Request;
// Import DB facade untuk menangani transaksi database multi-tabel (Database Transaction)
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan katalog makanan untuk sisi pelanggan (Customer View)
     */
    public function index()
    {
        // Mengambil semua menu makanan yang tersedia di database
        $foods = Food::all();

        // Membuka tampilan resources/views/customer/index.blade.php sambil membawa data makanan
        return view('customer.index', compact('foods'));
    }

    /**
     * Menyimpan transaksi pesanan baru dari pelanggan (Proses Checkout Multi-Item)
     */
    public function store(Request $request)
    {
        // 1. Validasi form identitas pemesan dan array item pesanan
        $request->validate([
            'customer_name' => 'required|string|max:255', // Nama pelanggan wajib diisi
            'table_number'  => 'required|string|max:255', // Nomor meja wajib diisi
            'items'         => 'required|array',          // Array item pesanan wajib ada
            'items.*'       => 'nullable|integer|min:0',  // Jumlah tiap item berupa bilangan bulat >= 0
        ]);

        // 2. Filter item: hanya ambil menu makanan yang porsinya lebih dari 0 (qty > 0)
        $orderedItems = array_filter($request->items, fn ($qty) => $qty > 0);

        // 3. Jika tidak ada menu makanan yang dipilih sama sekali, kembalikan dengan pesan error
        if (empty($orderedItems)) {
            return back()->with('error', 'Pilih minimal satu menu makanan!');
        }

        // 4. Memulai Database Transaction agar jika satu proses gagal, seluruh data otomatis dibatalkan (Rollback)
        DB::beginTransaction();
        try {
            // A. Buat data induk pesanan (Header Order) terlebih dahulu dengan total_price awal 0
            $order = Order::create([
                'customer_name' => $request->customer_name, // Simpan nama pelanggan
                'table_number'  => $request->table_number,  // Simpan nomor meja
                'total_price'   => 0,                       // Diinisialisasi 0, akan diupdate setelah hitung rincian
                'status'        => 'pending',               // Status awal transaksi adalah pending
            ]);

            // Inisialisasi variabel penghitung total keseluruhan harga pesanan
            $totalPrice = 0;

            // B. Melakukan perulangan untuk setiap item menu yang dipesan pelanggan
            foreach ($orderedItems as $foodId => $quantity) {
                // Cari data makanan di database berdasarkan ID-nya
                $food = Food::findOrFail($foodId);

                // Hitung subtotal untuk item ini (Harga Makanan x Jumlah Porsi)
                $subtotal = $food->price * $quantity;

                // Akumulasikan subtotal ke dalam grand total harga
                $totalPrice += $subtotal;

                // Simpan rincian pesanan ke tabel 'order_details' (Detail Transaksi)
                OrderDetail::create([
                    'order_id' => $order->id,  // Relasikan foreign key ke order induk
                    'food_id'  => $foodId,     // Relasikan foreign key ke makanan yang dipilih
                    'quantity' => $quantity,   // Simpan jumlah porsi yang dibeli
                    'subtotal' => $subtotal,   // Simpan total harga untuk menu ini
                ]);
            }

            // C. Perbarui total harga akhir pada tabel induk 'orders'
            $order->update(['total_price' => $totalPrice]);

            // D. Jika seluruh proses di atas berhasil tanpa error, simpan permanen ke database
            DB::commit();

            // E. Alihkan pelanggan kembali ke katalog dengan notifikasi sukses pesanan
            return redirect()->route('customer.index')->with('success', 'Pesanan berhasil dibuat! Nomor Meja: '.$order->table_number);
        } catch (\Exception $e) {
            // Jika terjadi kegagalan/error di tengah proses, batalkan seluruh perubahan database
            DB::rollBack();

            // Kembalikan ke halaman sebelumnya dengan menampilkan pesan error
            return back()->with('error', 'Gagal memproses pesanan: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan dashboard admin untuk rekap data pesanan yang masuk
     */
    public function adminDashboard()
    {
        // Mengambil semua data order terbaru beserta rincian pesanan dan informasi makanannya (Eager Loading)
        $orders = Order::with('orderDetails.food')->latest()->get();

        // Mengirimkan variabel $orders ke tampilan resources/views/dashboard.blade.php
        return view('dashboard', compact('orders'));
    }

    /**
     * Mengubah status pesanan oleh admin (Pending -> Selesai / Batalkan)
     */
    public function updateStatus(Request $request, $id)
    {
        // 1. Validasi input pilihan status dari formulir admin
        $request->validate(['status' => 'required|string']);

        // 2. Cari data pesanan berdasarkan ID
        $order = Order::findOrFail($id);

        // 3. Perbarui status pesanan sesuai pilihan admin (pending/completed/cancelled)
        $order->update(['status' => $request->status]);

        // 4. Kembalikan ke halaman sebelumnya dengan notifikasi sukses
        return back()->with('success', 'Status pesanan #'.$order->id.' berhasil diperbarui!');
    }
}
