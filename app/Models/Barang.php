<?php

// File: app/Models/Barang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    /**
     * Nama tabel yang terhubung dengan Model ini
     * Laravel otomatis mendeteksi nama tabel dari nama Model (jamak)
     * Tapi karena kita pakai nama 'barang' (bukan 'barangs'), kita tentukan manual
     */
    protected $table = 'barang';

    /**
     * $fillable: Daftar kolom yang boleh diisi secara massal (mass assignment)
     * Ini adalah fitur keamanan Laravel. Tanpa $fillable, data dari request
     * tidak bisa langsung dimasukkan ke database menggunakan create() atau update().
     *
     * Contoh: Barang::create($request->all())
     * Hanya kolom yang ada di $fillable yang akan disimpan.
     */
    protected $fillable = [
        'nama_barang',
        'kategori',
        'jumlah_total',
        'jumlah_tersedia',
        'kondisi',
        'status',
        'foto',
        'keterangan',
        'tanggal_hilang',
        'tanggal_selesai',
        'catatan_selesai',
        'last_checked_at',
    ];

    /**
     * $casts: Mengubah tipe data kolom saat diakses dari Model
     * Misal: 'tanggal_hilang' yang di database adalah string DATE,
     * akan otomatis diubah menjadi objek Carbon (library tanggal Laravel)
     */
    protected $casts = [
        'jumlah_total' => 'integer',
        'jumlah_tersedia' => 'integer',
        'tanggal_hilang' => 'date',
        'tanggal_selesai' => 'date',
        'last_checked_at' => 'datetime',
    ];

    /**
     * Relasi: Satu Barang memiliki banyak Pengecekan (one-to-many)
     *
     * Ini membuat kita bisa akses semua riwayat pengecekan dari sebuah barang:
     * $barang = Barang::find(1);
     * $riwayat = $barang->pengecekan; // mengembalikan koleksi pengecekan
     */
    public function pengecekan(): HasMany
    {
        return $this->hasMany(Pengecekan::class, 'barang_id');
    }

    /**
     * Accessor: Getter khusus untuk mendapatkan URL foto lengkap
     *
     * Accessor adalah fungsi yang dipanggil saat kita mengakses atribut tertentu.
     * Cara pakai: $barang->foto_url
     * Hasilnya: "http://localhost:8000/storage/barang/namafile.jpg"
     * atau null jika tidak ada foto.
     *
     * asset() adalah helper Laravel untuk menghasilkan URL lengkap ke folder public
     */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}