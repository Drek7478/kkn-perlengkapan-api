<?php

// File: database/migrations/xxxx_xx_xx_xxxxxx_create_pengecekan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengecekan', function (Blueprint $table) {
            // ID utama
            $table->id();

            // Foreign key ke tabel barang
            // foreignId() = BIGINT UNSIGNED
            // constrained('barang') = referensi ke tabel barang
            // onDelete('cascade') = jika barang dihapus, pengecekan terkait ikut terhapus
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade');

            // Foreign key ke tabel users (siapa yang melakukan pengecekan)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // DATE — tanggal pengecekan
            $table->date('tanggal_cek');

            // ENUM — kondisi saat dicek
            $table->enum('kondisi_cek', ['baik', 'rusak_ringan', 'rusak_berat']);

            // INT UNSIGNED — jumlah tersedia saat dicek
            $table->integer('jumlah_tersedia_cek')->unsigned();

            // TEXT NULL — catatan opsional
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengecekan');
    }
};