<?php

// File: app/Models/Pengecekan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengecekan extends Model
{
    protected $table = 'pengecekan';

    protected $fillable = [
        'barang_id',
        'user_id',
        'tanggal_cek',
        'kondisi_cek',
        'jumlah_tersedia_cek',
        'catatan',
    ];

    protected $casts = [
        'tanggal_cek' => 'date',
        'jumlah_tersedia_cek' => 'integer',
    ];

    /**
     * Relasi: Pengecekan dimiliki oleh satu Barang (many-to-one)
     *
     * $pengecekan = Pengecekan::find(1);
     * $namaBarang = $pengecekan->barang->nama_barang;
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Relasi: Pengecekan dilakukan oleh satu User
     *
     * $pengecekan = Pengecekan::find(1);
     * $namaUser = $pengecekan->user->name;
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}