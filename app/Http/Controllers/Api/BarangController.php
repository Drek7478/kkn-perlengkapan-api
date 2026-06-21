<?php

// File: app/Http/Controllers/Api/BarangController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Models\Barang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BarangController extends Controller
{
    /**
     * index(): Menampilkan daftar barang dengan filter & search
     *
     * Method: GET /api/barang?search=laptop&kategori=elektronik&kondisi=baik
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Mulai query dari model Barang
        $query = Barang::query();

        // Filter berdasarkan status 'aktif' (default) atau 'hilang' / 'selesai'
        // Ini penting agar halaman barang hanya menampilkan barang aktif
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: hanya tampilkan barang aktif
            $query->where('status', 'aktif');
        }

        // Search: mencari barang berdasarkan nama (LIKE %keyword%)
        // when() = jalankan fungsi di dalamnya hanya jika kondisi pertama true
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_barang', 'LIKE', "%{$search}%");
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan kondisi
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        // Urutkan dari yang terbaru, lalu ambil hasilnya
        $barang = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil diambil.',
            'data'    => $barang,
            'total'   => $barang->count(),
        ]);
    }

    /**
     * store(): Menyimpan barang baru + upload foto
     *
     * Method: POST /api/barang (multipart/form-data)
     *
     * @param StoreBarangRequest $request (validasi otomatis dijalankan)
     * @return JsonResponse
     */
    public function store(StoreBarangRequest $request): JsonResponse
    {
        // Ambil SEMUA data yang sudah divalidasi
        $data = $request->validated();

        // Cek apakah ada file foto yang diupload
        if ($request->hasFile('foto')) {
            // Simpan foto ke folder storage/app/public/barang/
            // Str::uuid() = menghasilkan string unik (misal: 550e8400-e29b-41d4-a716-446655440000)
            // extension() = mendapatkan ekstensi file asli (jpg, png, dll)
            $file     = $request->file('foto');
            $filename = Str::uuid() . '.' . $file->extension();

            // storeAs('folder_tujuan', 'nama_file', 'disk')
            // Ini menyimpan file dan mengembalikan path relatif: 'barang/namafile.jpg'
            $path = $file->storeAs('barang', $filename, 'public');

            // Simpan path ke data yang akan diinsert ke database
            $data['foto'] = $path;
        }

        // Set status default = 'aktif' untuk barang baru
        $data['status'] = 'aktif';

        // Buat record baru di database
        $barang = Barang::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditambahkan.',
            'data'    => $barang,
        ], 201); // 201 = Created
    }

    /**
     * show(): Menampilkan detail satu barang beserta riwayat pengecekan
     *
     * Method: GET /api/barang/{id}
     *
     * @param string $id (ID barang)
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        // Cari barang berdasarkan ID
        // with('pengecekan.user') = eager loading: ambil data pengecekan + data user sekaligus
        //                          Ini menghindari N+1 query problem
        $barang = Barang::with(['pengecekan' => function ($query) {
            // Urutkan riwayat pengecekan dari terbaru
            $query->orderBy('created_at', 'desc');
        }, 'pengecekan.user'])->find($id);

        // Jika barang tidak ditemukan, return 404
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail barang berhasil diambil.',
            'data'    => $barang,
        ]);
    }

    /**
     * update(): Mengedit barang (termasuk ganti foto)
     *
     * CATATAN PENTING: Karena frontend mengirim menggunakan FormData
     * dan method spoofing (_method=PUT), route-nya tetap POST
     * ke /api/barang/{id} dengan _method=PUT
     *
     * @param UpdateBarangRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateBarangRequest $request, string $id): JsonResponse
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        // Cek: barang yang sudah diselesaikan tidak boleh diedit
        if ($barang->status === 'selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Barang yang sudah diselesaikan tidak dapat diedit.',
            ], 403); // 403 = Forbidden
        }

        // Ambil data yang sudah divalidasi
        $data = $request->validated();

        // Cek apakah ada file foto baru yang diupload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($barang->foto) {
                // Storage::disk('public')->delete('path/file.jpg')
                // Ini menghapus file dari folder storage/app/public/
                Storage::disk('public')->delete($barang->foto);
            }

            // Simpan foto baru
            $file     = $request->file('foto');
            $filename = Str::uuid() . '.' . $file->extension();
            $path     = $file->storeAs('barang', $filename, 'public');

            $data['foto'] = $path;
        }

        // Update record di database
        $barang->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil diperbarui.',
            'data'    => $barang->fresh(), // fresh() = reload data dari database
        ]);
    }

    /**
     * destroy(): Menghapus barang beserta fotonya
     *
     * Method: DELETE /api/barang/{id}
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        // Hapus foto dari storage jika ada
        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }

        // Hapus record dari database
        // (pengecekan terkait akan ikut terhapus karena onDelete('cascade'))
        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dihapus.',
            'data'    => null,
        ]);
    }

    /**
     * tandaiHilang(): Menandai barang sebagai hilang
     *
     * Method: PATCH /api/barang/{id}/tandai-hilang
     *
     * @param string $id
     * @return JsonResponse
     */
    public function tandaiHilang(string $id): JsonResponse
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        // Update status dan tanggal hilang
        $barang->update([
            'status'         => 'hilang',
            'tanggal_hilang' => now()->toDateString(), // now() = tanggal & waktu saat ini
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil ditandai sebagai hilang.',
            'data'    => $barang->fresh(),
        ]);
    }

    /**
     * pulihkan(): Memulihkan barang hilang menjadi aktif kembali
     *
     * Method: PATCH /api/barang/{id}/pulihkan
     *
     * @param string $id
     * @return JsonResponse
     */
    public function pulihkan(string $id): JsonResponse
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        // Kembalikan status ke aktif dan hapus tanggal hilang
        $barang->update([
            'status'         => 'aktif',
            'tanggal_hilang' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dipulihkan.',
            'data'    => $barang->fresh(),
        ]);
    }

    /**
     * selesaikan(): Menandai barang sebagai selesai (arsip)
     *
     * Method: PATCH /api/barang/{id}/selesaikan
     *
     * @param string $id
     * @return JsonResponse
     */
    public function selesaikan(string $id): JsonResponse
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        // Update status, tanggal selesai, dan catatan
        $barang->update([
            'status'          => 'selesai',
            'tanggal_selesai' => now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil diselesaikan.',
            'data'    => $barang->fresh(),
        ]);
    }

    /**
     * selesaikanSemua(): Menandai SEMUA barang aktif sebagai selesai
     *
     * Method: POST /api/barang/selesaikan-semua
     *
     * @return JsonResponse
     */
    public function selesaikanSemua(): JsonResponse
    {
        // Ambil semua barang yang masih aktif
        $count = Barang::where('status', 'aktif')->count();

        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada barang aktif untuk diselesaikan.',
            ], 400);
        }

        // Update semua barang aktif menjadi selesai
        Barang::where('status', 'aktif')->update([
            'status'          => 'selesai',
            'tanggal_selesai' => now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$count} barang berhasil diselesaikan.",
            'data'    => null,
        ]);
    }

    /**
     * hilang(): Menampilkan daftar barang yang berstatus hilang
     *
     * Method: GET /api/barang-hilang
     *
     * @return JsonResponse
     */
    public function hilang(): JsonResponse
    {
        $barang = Barang::where('status', 'hilang')
            ->orderBy('tanggal_hilang', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data barang hilang berhasil diambil.',
            'data'    => $barang,
            'total'   => $barang->count(),
        ]);
    }

    /**
     * selesai(): Menampilkan daftar barang yang berstatus selesai (arsip)
     *
     * Method: GET /api/barang-selesai
     *
     * @return JsonResponse
     */
    public function selesai(): JsonResponse
    {
        $barang = Barang::where('status', 'selesai')
            ->orderBy('tanggal_selesai', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data barang selesai berhasil diambil.',
            'data'    => $barang,
            'total'   => $barang->count(),
        ]);
    }
}