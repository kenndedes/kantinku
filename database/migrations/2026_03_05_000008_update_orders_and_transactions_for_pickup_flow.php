<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('order_number')->nullable()->unique()->after('id');
            $table->string('pickup_code', 10)->nullable()->unique()->after('status');
            $table->timestamp('completed_at')->nullable()->after('pickup_code');
        });

        DB::statement("ALTER TABLE orders MODIFY status ENUM('menunggu','diproses','siap_diambil','selesai') DEFAULT 'menunggu'");

        Schema::table('transactions', function (Blueprint $table): void {
            $table->foreignId('order_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });

        DB::statement("ALTER TABLE orders MODIFY status ENUM('menunggu','diproses','siap_diambil') DEFAULT 'menunggu'");

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['order_number', 'pickup_code', 'completed_at']);
        });
    }
};
