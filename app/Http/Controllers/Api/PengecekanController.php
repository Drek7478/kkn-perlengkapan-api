<?php

// File: app/Http/Controllers/Api/PengecekanController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengecekanRequest;
use App\Models\Barang;
use App\Models\Pengecekan;
use Illuminate\Http\JsonResponse;

class PengecekanController extends Controller
{
    /**
     * indexByBarang(): Menampilkan riwayat pengecekan untuk satu barang
     *
     * Method: GET /api/pengecekan/barang/{barang_id}
     * Ini dipanggil saat membuka halaman detail barang
     *
     * @param string $barang_id
     * @return JsonResponse
     */
    public function indexByBarang(string $barang_id): JsonResponse
    {
        // Cek apakah barangnya ada
        $barang = Barang::find($barang_id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        // Ambil semua pengecekan untuk barang ini
        // with('user') = sekalian ambil data user yang melakukan pengecekan
        $pengecekan = Pengecekan::where('barang_id', $barang_id)
            ->with('user:id,name') // hanya ambil id & name dari user (lebih efisien)
            ->orderBy('tanggal_cek', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pengecekan berhasil diambil.',
            'data'    => $pengecekan,
            'total'   => $pengecekan->count(),
        ]);
    }

    /**
     * store(): Menyimpan pengecekan baru + mengupdate kondisi barang
     *
     * Method: POST /api/pengecekan
     *
     * LOGIKA BISNIS:
     * Setelah pengecekan disimpan:
     * 1. Update barang.kondisi = kondisi_cek
     * 2. Update barang.jumlah_tersedia = jumlah_tersedia_cek
     * 3. Update barang.last_checked_at = now()
     *
     * @param StorePengecekanRequest $request
     * @return JsonResponse
     */
    public function store(StorePengecekanRequest $request): JsonResponse
    {
        // Ambil data yang sudah divalidasi
        $data = $request->validated();

        // Tambahkan user_id (siapa yang melakukan pengecekan)
        // $request->user() = user yang sedang login (dari token Sanctum)
        $data['user_id'] = $request->user()->id;

        // Simpan pengecekan baru ke database
        $pengecekan = Pengecekan::create($data);

        // ============================================
        // LOGIKA BISNIS: Update kondisi barang
        // ============================================

        $barang = Barang::find($data['barang_id']);

        if ($barang) {
            $barang->update([
                // Update kondisi sesuai hasil pengecekan
                'kondisi'          => $data['kondisi_cek'],
                // Update jumlah tersedia sesuai hasil pengecekan
                'jumlah_tersedia'  => $data['jumlah_tersedia_cek'],
                // Catat waktu pengecekan terakhir
                'last_checked_at'  => now(),
            ]);
        }

        // Kembalikan response dengan data pengecekan + data user
        return response()->json([
            'success' => true,
            'message' => 'Pengecekan berhasil disimpan.',
            'data'    => $pengecekan->load('user:id,name'),
        ], 201);
    }
}