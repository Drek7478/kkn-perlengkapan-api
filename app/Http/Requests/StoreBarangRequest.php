<?php

// File: app/Http/Requests/StoreBarangRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    /**
     * authorize(): Menentukan apakah user boleh mengirim request ini
     * Karena semua user yang sudah login (admin) boleh tambah barang,
     * kita return true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * rules(): Aturan validasi untuk setiap field
     *
     * Format: 'nama_field' => ['aturan1', 'aturan2', ...]
     * Atau:   'nama_field' => 'aturan1|aturan2|...'
     */
    public function rules(): array
    {
        return [
            // required = wajib diisi
            // string   = harus berupa teks
            // max:150  = maksimal 150 karakter
            'nama_barang' => ['required', 'string', 'max:150'],

            'kategori'    => ['required', 'string', 'max:100'],

            // integer   = harus angka bulat
            // min:0     = minimal 0 (tidak boleh negatif)
            'jumlah_total'    => ['required', 'integer', 'min:0'],
            'jumlah_tersedia' => ['required', 'integer', 'min:0'],

            // in:baik,rusak_ringan,rusak_berat = hanya boleh salah satu dari nilai ini
            'kondisi' => ['required', 'in:baik,rusak_ringan,rusak_berat'],

            'keterangan' => ['nullable', 'string'],

            // nullable = boleh kosong
            // image    = harus berupa gambar
            // mimes    = hanya boleh format jpg, jpeg, png
            // max:2048 = maksimal 2MB (2048 kilobyte)
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * messages(): Pesan error kustom dalam Bahasa Indonesia
     */
    public function messages(): array
    {
        return [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.max'      => 'Nama barang maksimal 150 karakter.',
            'kategori.required'    => 'Kategori wajib diisi.',
            'kategori.max'         => 'Kategori maksimal 100 karakter.',
            'jumlah_total.required'    => 'Jumlah total wajib diisi.',
            'jumlah_total.integer'     => 'Jumlah total harus berupa angka.',
            'jumlah_total.min'         => 'Jumlah total tidak boleh negatif.',
            'jumlah_tersedia.required' => 'Jumlah tersedia wajib diisi.',
            'jumlah_tersedia.integer'  => 'Jumlah tersedia harus berupa angka.',
            'jumlah_tersedia.min'      => 'Jumlah tersedia tidak boleh negatif.',
            'kondisi.required' => 'Kondisi wajib dipilih.',
            'kondisi.in'       => 'Kondisi tidak valid.',
            'foto.image'       => 'File harus berupa gambar.',
            'foto.mimes'       => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto.max'         => 'Ukuran foto maksimal 2MB.',
        ];
    }
}