<?php

// File: app/Http/Controllers/Api/DashboardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Pengecekan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * index(): Menampilkan data dashboard
     *
     * Method: GET /api/dashboard
     *
     * Data yang ditampilkan:
     * - Statistik: total aktif, kondisi baik/rusak/hilang/selesai
     * - Barang yang belum dicek > 3 hari (peringatan)
     * - Aktivitas pengecekan terbaru
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // ============================================
        // 1. STATISTIK BARANG
        // ============================================

        // Menghitung jumlah barang per status
        $totalAktif  = Barang::where('status', 'aktif')->count();
        $totalHilang = Barang::where('status', 'hilang')->count();
        $totalSelesai = Barang::where('status', 'selesai')->count();

        // Menghitung jumlah barang per kondisi (hanya yang aktif)
        $kondisiBaik       = Barang::where('status', 'aktif')->where('kondisi', 'baik')->count();
        $kondisiRusakRingan = Barang::where('status', 'aktif')->where('kondisi', 'rusak_ringan')->count();
        $kondisiRusakBerat  = Barang::where('status', 'aktif')->where('kondisi', 'rusak_berat')->count();

        // ============================================
        // 2. BARANG YANG PERLU DICEK (> 3 HARI)
        // ============================================

        // Logika:
        // - Status = 'aktif'
        // - DAN (last_checked_at < 3 hari yang lalu ATAU last_checked_at NULL/belum pernah dicek)
        $perluDicek = Barang::where('status', 'aktif')
            ->where(function ($query) {
                // Barang yang belum pernah dicek (last_checked_at = NULL)
                $query->whereNull('last_checked_at')
                    // ATAU barang yang terakhir dicek lebih dari 3 hari yang lalu
                    ->orWhere('last_checked_at', '<', now()->subDays(3));
            })
            ->orderBy('last_checked_at', 'asc') // Urutkan dari yang paling lama tidak dicek
            ->get();

        // ============================================
        // 3. AKTIVITAS PENGECEKAN TERBARU
        // ============================================

        // Ambil 5 pengecekan terakhir dengan data barang dan user
        $pengecekanTerbaru = Pengecekan::with(['barang:id,nama_barang,foto', 'user:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ============================================
        // 4. TOTAL BARANG KESELURUHAN
        // ============================================
        $totalSemuaBarang = Barang::count();

        // ============================================
        // RESPONSE
        // ============================================
        return response()->json([
            'success' => true,
            'message' => 'Data dashboard berhasil diambil.',
            'data'    => [
                'statistik' => [
                    'total_aktif'    => $totalAktif,
                    'total_hilang'   => $totalHilang,
                    'total_selesai'  => $totalSelesai,
                    'total_semua'    => $totalSemuaBarang,
                    'kondisi_baik'        => $kondisiBaik,
                    'kondisi_rusak_ringan' => $kondisiRusakRingan,
                    'kondisi_rusak_berat'  => $kondisiRusakBerat,
                ],
                'perlu_dicek'         => $perluDicek,
                'pengecekan_terbaru'  => $pengecekanTerbaru,
            ],
        ]);
    }
}