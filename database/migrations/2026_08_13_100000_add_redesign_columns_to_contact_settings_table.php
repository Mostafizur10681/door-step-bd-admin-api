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
        Schema::table('contact_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_settings', 'badge_text')) {
                $table->string('badge_text')->nullable()->after('id');
            }
            if (!Schema::hasColumn('contact_settings', 'hero_title')) {
                $table->string('hero_title')->nullable()->after('badge_text');
            }
            if (!Schema::hasColumn('contact_settings', 'hero_subtitle')) {
                $table->text('hero_subtitle')->nullable()->after('hero_title');
            }
            if (!Schema::hasColumn('contact_settings', 'secondary_phone')) {
                $table->string('secondary_phone')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('contact_settings', 'secondary_email')) {
                $table->string('secondary_email')->nullable()->after('email');
            }
            if (!Schema::hasColumn('contact_settings', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('secondary_phone');
            }
            if (!Schema::hasColumn('contact_settings', 'response_time_note')) {
                $table->string('response_time_note')->nullable()->after('business_hours_weekend');
            }
            if (!Schema::hasColumn('contact_settings', 'map_title')) {
                $table->string('map_title')->nullable()->after('response_time_note');
            }
            if (!Schema::hasColumn('contact_settings', 'map_subtitle')) {
                $table->text('map_subtitle')->nullable()->after('map_title');
            }
            if (!Schema::hasColumn('contact_settings', 'map_url')) {
                $table->longText('map_url')->nullable()->after('map_subtitle');
            }
            if (!Schema::hasColumn('contact_settings', 'location_directions')) {
                $table->text('location_directions')->nullable()->after('map_url');
            }
            if (!Schema::hasColumn('contact_settings', 'form_title')) {
                $table->string('form_title')->nullable()->after('location_directions');
            }
            if (!Schema::hasColumn('contact_settings', 'form_subtitle')) {
                $table->text('form_subtitle')->nullable()->after('form_title');
            }
            if (!Schema::hasColumn('contact_settings', 'form_topics')) {
                $table->json('form_topics')->nullable()->after('form_subtitle');
            }
            if (!Schema::hasColumn('contact_settings', 'emergency_notice')) {
                $table->text('emergency_notice')->nullable()->after('form_topics');
            }
            if (!Schema::hasColumn('contact_settings', 'features')) {
                $table->json('features')->nullable()->after('emergency_notice');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $columns = [
                'badge_text',
                'hero_title',
                'hero_subtitle',
                'secondary_phone',
                'secondary_email',
                'whatsapp_number',
                'response_time_note',
                'map_title',
                'map_subtitle',
                'map_url',
                'location_directions',
                'form_title',
                'form_subtitle',
                'form_topics',
                'emergency_notice',
                'features'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('contact_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
