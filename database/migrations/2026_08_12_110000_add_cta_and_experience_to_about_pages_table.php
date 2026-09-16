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
        if (Schema::hasColumn('about_pages', 'experience_badge_text')) { return; }

                Schema::table('about_pages', function (Blueprint $table) {
            $table->string('experience_badge_text')->nullable()->after('story_since');
            $table->string('experience_badge_subtext')->nullable()->after('experience_badge_text');
            $table->string('cta_title')->nullable()->after('team');
            $table->text('cta_subtitle')->nullable()->after('cta_title');
            $table->string('cta_phone')->nullable()->after('cta_subtitle');
            $table->string('cta_email')->nullable()->after('cta_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_pages', function (Blueprint $table) {
            $table->dropColumn([
                'experience_badge_text',
                'experience_badge_subtext',
                'cta_title',
                'cta_subtitle',
                'cta_phone',
                'cta_email',
            ]);
        });
    }
};
