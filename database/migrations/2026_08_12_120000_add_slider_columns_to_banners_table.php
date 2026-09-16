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
        // Skip if already applied
        if (Schema::hasColumn('banners', 'title_line1')) { return; }

                Schema::table('banners', function (Blueprint $table) {
            $table->string('title_line1')->nullable()->after('title');
            $table->string('title_line2')->nullable()->after('title_line1');
            $table->longText('left_image')->nullable()->after('image');
            $table->string('bg_color')->default('#dfcebe')->after('left_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn([
                'title_line1',
                'title_line2',
                'left_image',
                'bg_color',
            ]);
        });
    }
};
