<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('menunggu','diproses','siap_diambil','selesai','batal') DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY status ENUM('menunggu','diproses','siap_diambil','selesai') DEFAULT 'menunggu'");
    }
};
