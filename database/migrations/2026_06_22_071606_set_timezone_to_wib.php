<?php

// File: database/migrations/xxxx_xx_xx_xxxxxx_set_timezone_to_wib.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set timezone MySQL ke WIB
        DB::statement("SET GLOBAL time_zone = '+07:00'");
        DB::statement("SET time_zone = '+07:00'");
    }

    public function down(): void
    {
        // Kembalikan ke UTC jika rollback
        DB::statement("SET GLOBAL time_zone = '+00:00'");
        DB::statement("SET time_zone = '+00:00'");
    }
};