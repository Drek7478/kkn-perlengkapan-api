<?php

// File: database/migrations/xxxx_xx_xx_xxxxxx_create_barang_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fungsi up() dijalankan saat migrate
     * Di sini kita mendefinisikan struktur tabel barang
     */
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->id();

            // VARCHAR(150) — nama barang
            $table->string('nama_barang', 150);

            // VARCHAR(100) — kategori (misal: elektronik, alat tulis, dll)
            $table->string('kategori', 100);

            // INT UNSIGNED DEFAULT 0 — jumlah total barang
            $table->integer('jumlah_total')->unsigned()->default(0);

            // INT UNSIGNED DEFAULT 0 — jumlah yang masih tersedia
            $table->integer('jumlah_tersedia')->unsigned()->default(0);

            // ENUM — hanya bisa berisi 'baik', 'rusak_ringan', atau 'rusak_berat'
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');

            // ENUM — status barang: aktif, hilang, atau selesai
            $table->enum('status', ['aktif', 'hilang', 'selesai'])->default('aktif');

            // VARCHAR(255) NULL — path foto di storage
            $table->string('foto', 255)->nullable();

            // TEXT NULL — keterangan tambahan
            $table->text('keterangan')->nullable();

            // DATE NULL — tanggal saat barang ditandai hilang
            $table->date('tanggal_hilang')->nullable();

            // DATE NULL — tanggal saat barang ditandai selesai
            $table->date('tanggal_selesai')->nullable();

            // TEXT NULL — catatan saat barang diselesaikan
            $table->text('catatan_selesai')->nullable();

            // TIMESTAMP NULL — waktu terakhir kali dicek
            $table->timestamp('last_checked_at')->nullable();

            // created_at dan updated_at (otomatis dikelola Laravel)
            $table->timestamps();
        });
    }

    /**
     * Fungsi down() dijalankan saat rollback (undo migrate)
     * Menghapus tabel barang
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};