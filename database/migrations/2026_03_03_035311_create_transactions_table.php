<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ref_id')->unique();
            $table->string('transaction_id')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->string('channel_code')->default('QRIS');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_status')->nullable();
            $table->text('qris_text')->nullable();
            $table->string('qris_url')->nullable();
            $table->string('provider_ref')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('ref_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
