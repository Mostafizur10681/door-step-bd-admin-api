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
        if (!Schema::hasColumn('banners', 'mobile_image')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->longText('mobile_image')->nullable()->after('left_image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('banners', 'mobile_image')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('mobile_image');
            });
        }
    }
};
