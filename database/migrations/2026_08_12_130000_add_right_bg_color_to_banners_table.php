<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds right_bg_color so each panel can have its own background color.
     */
    public function up(): void
    {
        // Skip if already applied
        if (Schema::hasColumn('banners', 'right_bg_color')) { return; }

                Schema::table('banners', function (Blueprint $table) {
            $table->string('right_bg_color')->default('#f5ece3')->after('bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('right_bg_color');
        });
    }
};
