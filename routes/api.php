<?php

// File: routes/api.php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\PengecekanController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - KKN Perlengkapan
|--------------------------------------------------------------------------
|
| Semua endpoint API untuk aplikasi manajemen perlengkapan KKN.
| Base URL: http://localhost:8000/api
|
*/

// ========================================
// ROUTE PUBLIC (TANPA LOGIN)
// ========================================

// POST /api/login
// Menerima email & password, mengembalikan token Sanctum
Route::post('/login', [AuthController::class, 'login']);

// ========================================
// ROUTE YANG MEMBUTUHKAN LOGIN (TOKEN)
// Semua route di dalam group ini memerlukan header:
// Authorization: Bearer [token]
// ========================================

Route::middleware('auth:sanctum')->group(function () {

    // ========================================
    // AUTH
    // ========================================

    // POST /api/logout
    // Menghapus token yang sedang digunakan
    Route::post('/logout', [AuthController::class, 'logout']);

    // ========================================
    // DASHBOARD
    // ========================================

    // GET /api/dashboard
    // Statistik, barang perlu dicek, aktivitas terbaru
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ========================================
    // BARANG - CRUD
    // ========================================

    // GET /api/barang
    // Daftar barang (default: status aktif)
    // Query params opsional: ?search=&kategori=&kondisi=&status=
    Route::get('/barang', [BarangController::class, 'index']);

    // POST /api/barang
    // Tambah barang baru (multipart/form-data, bisa upload foto)
    Route::post('/barang', [BarangController::class, 'store']);

    // ========================================
    // BARANG - RUTE SPESIFIK (HARUS DI ATAS RUTE PARAMETER)
    // ========================================

    // POST /api/barang/selesaikan-semua
    // Mengarsipkan semua barang aktif sekaligus
    Route::post('/barang/selesaikan-semua', [BarangController::class, 'selesaikanSemua']);

    // ========================================
    // BARANG - RUTE DENGAN PARAMETER {id}
    // ========================================

    // GET /api/barang/{id}
    // Detail barang + riwayat pengecekan
    Route::get('/barang/{id}', [BarangController::class, 'show']);

    // POST /api/barang/{id}
    // Edit barang (pakai _method=PUT di FormData untuk upload foto)
    Route::post('/barang/{id}', [BarangController::class, 'update']);

    // DELETE /api/barang/{id}
    // Hapus barang beserta fotonya
    Route::delete('/barang/{id}', [BarangController::class, 'destroy']);

    // PATCH /api/barang/{id}/tandai-hilang
    // Tandai barang sebagai hilang
    Route::patch('/barang/{id}/tandai-hilang', [BarangController::class, 'tandaiHilang']);

    // PATCH /api/barang/{id}/pulihkan
    // Pulihkan barang hilang ke status aktif
    Route::patch('/barang/{id}/pulihkan', [BarangController::class, 'pulihkan']);

    // PATCH /api/barang/{id}/selesaikan
    // Arsipkan satu barang ke status selesai
    Route::patch('/barang/{id}/selesaikan', [BarangController::class, 'selesaikan']);

    // ========================================
    // BARANG - HALAMAN KHUSUS
    // ========================================

    // GET /api/barang-hilang
    // Daftar semua barang dengan status hilang
    Route::get('/barang-hilang', [BarangController::class, 'hilang']);

    // GET /api/barang-selesai
    // Daftar semua barang dengan status selesai (arsip)
    Route::get('/barang-selesai', [BarangController::class, 'selesai']);

    // ========================================
    // PENGECEKAN
    // ========================================

    // GET /api/pengecekan/barang/{barang_id}
    // Riwayat pengecekan untuk satu barang tertentu
    Route::get('/pengecekan/barang/{barang_id}', [PengecekanController::class, 'indexByBarang']);

    // POST /api/pengecekan
    // Simpan pengecekan baru + update kondisi & last_checked_at barang
    Route::post('/pengecekan', [PengecekanController::class, 'store']);

});