<?php

// File: database/seeders/AdminSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Jalankan seeder ini
     */
    public function run(): void
    {
        // Membuat user admin baru
        User::create([
            'name'     => 'Ketua Perlengkapan',
            'email'    => 'admin@kkn.com',
            // Hash::make() = mengenkripsi password (wajib! jangan simpan password mentah)
            'password' => Hash::make('password123'),
        ]);

        // Menampilkan pesan di terminal (opsional, untuk konfirmasi)
        $this->command->info('Admin berhasil dibuat!');
        $this->command->info('Email: admin@kkn.com');
        $this->command->info('Password: password123');
    }
}