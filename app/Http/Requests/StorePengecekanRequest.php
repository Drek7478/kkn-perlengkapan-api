<?php

// File: app/Http/Requests/StorePengecekanRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePengecekanRequest extends FormRequest
{
    /**
     * authorize(): Admin yang login boleh melakukan pengecekan
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * rules(): Aturan validasi untuk form pengecekan
     */
    public function rules(): array
    {
        return [
            // exists:barang,id = memastikan barang_id benar-benar ada di tabel barang
            'barang_id' => ['required', 'exists:barang,id'],

            // date_format:Y-m-d = format tanggal harus YYYY-MM-DD (contoh: 2024-01-15)
            'tanggal_cek' => ['required', 'date', 'date_format:Y-m-d'],

            'kondisi_cek' => ['required', 'in:baik,rusak_ringan,rusak_berat'],

            // integer, min:0
            'jumlah_tersedia_cek' => ['required', 'integer', 'min:0'],

            'catatan' => ['nullable', 'string'],
        ];
    }

    /**
     * messages(): Pesan error Bahasa Indonesia
     */
    public function messages(): array
    {
        return [
            'barang_id.required' => 'Barang wajib dipilih.',
            'barang_id.exists'   => 'Barang tidak ditemukan.',
            'tanggal_cek.required'     => 'Tanggal pengecekan wajib diisi.',
            'tanggal_cek.date'         => 'Format tanggal tidak valid.',
            'tanggal_cek.date_format'  => 'Format tanggal harus YYYY-MM-DD.',
            'kondisi_cek.required'     => 'Kondisi wajib dipilih.',
            'kondisi_cek.in'           => 'Kondisi tidak valid.',
            'jumlah_tersedia_cek.required' => 'Jumlah tersedia wajib diisi.',
            'jumlah_tersedia_cek.integer'  => 'Jumlah tersedia harus berupa angka.',
            'jumlah_tersedia_cek.min'      => 'Jumlah tersedia tidak boleh negatif.',
        ];
    }
}