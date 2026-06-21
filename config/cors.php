<?php

// File: config/cors.php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini menentukan domain mana yang boleh mengakses API.
    | Karena React berjalan di port 5173 dan Laravel di port 8000,
    | kita harus mengizinkan localhost:5173.
    |
    */

    // Domain yang diizinkan mengakses API
    'allowed_origins' => ['http://localhost:5173'],

    // Pola path yang diizinkan (bisa pakai * untuk semua)
    'paths' => ['api/*'],

    // HTTP methods yang diizinkan (* = semua: GET, POST, PUT, DELETE, dll)
    'allowed_methods' => ['*'],

    // Header yang diizinkan (* = semua)
    'allowed_headers' => ['*'],

    // Apakah response boleh mengekspose header tertentu
    'exposed_headers' => [],

    // Berapa lama hasil preflight request di-cache (dalam detik)
    'max_age' => 0,

    // Apakah credentials (cookies, authorization header) diizinkan
    'supports_credentials' => false,
];