<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users: allow seller role
        // Keep enum but extend options to include seller
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','seller','user') DEFAULT 'user'");

        // Menu items: assign stand + stock (guard existing columns)
        Schema::table('menu_items', function (Blueprint $table) {
            if (! Schema::hasColumn('menu_items', 'stand_id')) {
                $table->foreignId('stand_id')->nullable()->after('id')->constrained('stands')->nullOnDelete();
            }

            if (! Schema::hasColumn('menu_items', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('price');
            }
        });

        // Orders: bind to stand, add statuses, pickup fields (guard columns that may exist from prior migration)
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'stand_id')) {
                $table->foreignId('stand_id')->nullable()->after('user_id')->constrained('stands')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'order_status')) {
                $table->enum('order_status', ['menunggu', 'diproses', 'siap_diambil', 'selesai', 'batal'])->default('menunggu')->after('pickup_time');
            }

            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'pending', 'paid', 'failed', 'refunded'])->default('unpaid')->after('order_status');
            }

            if (! Schema::hasColumn('orders', 'pickup_code')) {
                $table->string('pickup_code', 20)->nullable()->unique()->after('payment_status');
            }

            if (! Schema::hasColumn('orders', 'pickup_qr_payload')) {
                $table->text('pickup_qr_payload')->nullable()->after('pickup_code');
            }

            if (! Schema::hasColumn('orders', 'ready_at')) {
                $table->timestamp('ready_at')->nullable()->after('pickup_qr_payload');
            }

            if (! Schema::hasColumn('orders', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable()->after('ready_at');
            }

            if (! Schema::hasColumn('orders', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('picked_up_at')->constrained('users')->nullOnDelete();
            }
        });

        // Order items: snapshot fields (+ stand snapshot for audit)
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'stand_id')) {
                $table->foreignId('stand_id')->nullable()->after('menu_item_id')->constrained('stands')->nullOnDelete();
            }
            if (! Schema::hasColumn('order_items', 'item_name_snapshot')) {
                $table->string('item_name_snapshot', 150)->nullable()->after('stand_id');
            }
            if (! Schema::hasColumn('order_items', 'price_snapshot')) {
                $table->decimal('price_snapshot', 12, 2)->nullable()->after('item_name_snapshot');
            }
            if (! Schema::hasColumn('order_items', 'image_snapshot')) {
                $table->string('image_snapshot')->nullable()->after('price_snapshot');
            }
        });

        // Transactions: attach order reference (guard if already exists)
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('user_id')->constrained('orders')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'stand_id')) {
                $table->dropForeign(['stand_id']);
            }

            $drops = array_filter([
                Schema::hasColumn('order_items', 'stand_id') ? 'stand_id' : null,
                Schema::hasColumn('order_items', 'item_name_snapshot') ? 'item_name_snapshot' : null,
                Schema::hasColumn('order_items', 'price_snapshot') ? 'price_snapshot' : null,
                Schema::hasColumn('order_items', 'image_snapshot') ? 'image_snapshot' : null,
            ]);

            if ($drops) {
                $table->dropColumn($drops);
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'stand_id')) {
                $table->dropForeign(['stand_id']);
                $table->dropColumn('stand_id');
            }
            if (Schema::hasColumn('orders', 'verified_by')) {
                $table->dropForeign(['verified_by']);
                $table->dropColumn('verified_by');
            }
            $table->dropColumn(array_filter([
                Schema::hasColumn('orders', 'order_status') ? 'order_status' : null,
                Schema::hasColumn('orders', 'payment_status') ? 'payment_status' : null,
                Schema::hasColumn('orders', 'pickup_code') ? 'pickup_code' : null,
                Schema::hasColumn('orders', 'pickup_qr_payload') ? 'pickup_qr_payload' : null,
                Schema::hasColumn('orders', 'ready_at') ? 'ready_at' : null,
                Schema::hasColumn('orders', 'picked_up_at') ? 'picked_up_at' : null,
            ]));
        });

        Schema::table('menu_items', function (Blueprint $table) {
            if (Schema::hasColumn('menu_items', 'stand_id')) {
                $table->dropForeign(['stand_id']);
            }

            $drops = array_filter([
                Schema::hasColumn('menu_items', 'stand_id') ? 'stand_id' : null,
                Schema::hasColumn('menu_items', 'stock') ? 'stock' : null,
            ]);

            if ($drops) {
                $table->dropColumn($drops);
            }
        });

        // Revert users role column
        DB::statement("ALTER TABLE users MODIFY role ENUM('admin','user') DEFAULT 'user'");
    }
};
