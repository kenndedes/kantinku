<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('stand_id')->nullable()->constrained('stands')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('phone', 30)->nullable();
            $table->json('documents')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['stand_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
