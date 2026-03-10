<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method')->default('manual')->after('channel_code');
            $table->string('proof_image')->nullable()->after('payment_method');
            $table->text('admin_note')->nullable()->after('proof_image');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('admin_note');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['payment_method', 'proof_image', 'admin_note', 'reviewed_by', 'reviewed_at']);
        });
    }
};
