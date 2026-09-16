<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->default(0.00)->nullable()->after('sale_price');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
        });

        // Convert existing status data and change column type to VARCHAR
        try {
            DB::statement("ALTER TABLE `products` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'active'");
            DB::statement("UPDATE `products` SET `status` = 'active' WHERE `status` = '1' OR `status` = '' OR `status` IS NULL");
            DB::statement("UPDATE `products` SET `status` = 'inactive' WHERE `status` = '0'");
        } catch (\Throwable $e) {
            // Fallback for non-MySQL or different drivers
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
            if (Schema::hasColumn('products', 'barcode')) {
                $table->dropColumn('barcode');
            }
        });

        try {
            DB::statement("ALTER TABLE `products` MODIFY `status` TINYINT(1) NOT NULL DEFAULT 1");
        } catch (\Throwable $e) {}
    }
};
