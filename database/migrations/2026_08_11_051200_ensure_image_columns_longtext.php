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
        if (Schema::hasTable('product_images')) {
            try {
                DB::statement('ALTER TABLE `product_images` MODIFY `image_path` LONGTEXT');
            } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'image')) {
            try {
                DB::statement('ALTER TABLE `products` MODIFY `image` LONGTEXT NULL');
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
