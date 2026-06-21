// File: config/cors.php

<?php

return [
    // Ganti jadi ini:
    'allowed_origins' => [
        'http://localhost:5173',
        'https://kkn-perlengkapan-frontend.vercel.app', // ← Tambahkan URL Vercel
    ],

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];