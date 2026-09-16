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
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('reviews', 'author_name')) {
                    $table->string('author_name')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('reviews', 'author_designation')) {
                    $table->string('author_designation')->nullable()->after('author_name');
                }
            });

            // Modify user_id to be nullable and status to VARCHAR(50)
            try {
                DB::statement('ALTER TABLE `reviews` MODIFY `user_id` BIGINT UNSIGNED NULL');
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE `reviews` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'approved'");
                DB::statement("UPDATE `reviews` SET `status` = 'approved' WHERE `status` = '1' OR `status` = '' OR `status` IS NULL");
                DB::statement("UPDATE `reviews` SET `status` = 'pending' WHERE `status` = '0'");
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (Schema::hasColumn('reviews', 'author_name')) {
                    $table->dropColumn('author_name');
                }
                if (Schema::hasColumn('reviews', 'author_designation')) {
                    $table->dropColumn('author_designation');
                }
            });
        }
    }
};
