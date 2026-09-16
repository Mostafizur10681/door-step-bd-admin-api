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
        if (Schema::hasTable('banners')) {
            // 1. Ensure all image columns are LONGTEXT to store large Base64 strings / Data URIs without truncation
            try {
                DB::statement('ALTER TABLE `banners` MODIFY `image` LONGTEXT NULL');
            } catch (\Throwable $e) {
                try {
                    Schema::table('banners', function (Blueprint $table) {
                        $table->longText('image')->nullable()->change();
                    });
                } catch (\Throwable $e2) {}
            }

            if (Schema::hasColumn('banners', 'mobile_image')) {
                try {
                    DB::statement('ALTER TABLE `banners` MODIFY `mobile_image` LONGTEXT NULL');
                } catch (\Throwable $e) {}
            } else {
                Schema::table('banners', function (Blueprint $table) {
                    $table->longText('mobile_image')->nullable()->after('image');
                });
            }

            if (Schema::hasColumn('banners', 'left_image')) {
                try {
                    DB::statement('ALTER TABLE `banners` MODIFY `left_image` LONGTEXT NULL');
                } catch (\Throwable $e) {}
            } else {
                Schema::table('banners', function (Blueprint $table) {
                    $table->longText('left_image')->nullable()->after('image');
                });
            }

            if (!Schema::hasColumn('banners', 'menu_location')) {
                Schema::table('banners', function (Blueprint $table) {
                    $table->string('menu_location')->nullable()->default('home_hero_slider')->after('is_active');
                });
            }

            if (!Schema::hasColumn('banners', 'right_bg_color')) {
                Schema::table('banners', function (Blueprint $table) {
                    $table->string('right_bg_color')->default('#002B49')->after('bg_color');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
