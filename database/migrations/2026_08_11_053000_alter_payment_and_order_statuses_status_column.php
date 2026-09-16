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
        if (Schema::hasTable('payment_statuses')) {
            try {
                DB::statement("ALTER TABLE `payment_statuses` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'active'");
                DB::statement("UPDATE `payment_statuses` SET `status` = 'active' WHERE `status` = '1' OR `status` = '' OR `status` IS NULL");
                DB::statement("UPDATE `payment_statuses` SET `status` = 'inactive' WHERE `status` = '0'");
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('order_statuses')) {
            try {
                DB::statement("ALTER TABLE `order_statuses` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'active'");
                DB::statement("UPDATE `order_statuses` SET `status` = 'active' WHERE `status` = '1' OR `status` = '' OR `status` IS NULL");
                DB::statement("UPDATE `order_statuses` SET `status` = 'inactive' WHERE `status` = '0'");
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
