<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('footer_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('footer_settings', 'logo_image')) {
                $table->longText('logo_image')->nullable()->after('store_name');
            }
            if (!Schema::hasColumn('footer_settings', 'address')) {
                $table->text('address')->nullable()->after('logo_image');
            }
            if (!Schema::hasColumn('footer_settings', 'map_url')) {
                $table->text('map_url')->nullable()->after('address');
            }
            if (!Schema::hasColumn('footer_settings', 'working_hours_1')) {
                $table->string('working_hours_1')->nullable()->after('contact_hours');
            }
            if (!Schema::hasColumn('footer_settings', 'working_hours_2')) {
                $table->string('working_hours_2')->nullable()->after('working_hours_1');
            }
            if (!Schema::hasColumn('footer_settings', 'facebook_url')) {
                $table->string('facebook_url')->nullable()->after('working_hours_2');
            }
            if (!Schema::hasColumn('footer_settings', 'instagram_url')) {
                $table->string('instagram_url')->nullable()->after('facebook_url');
            }
            if (!Schema::hasColumn('footer_settings', 'youtube_url')) {
                $table->string('youtube_url')->nullable()->after('instagram_url');
            }
            if (!Schema::hasColumn('footer_settings', 'pinterest_url')) {
                $table->string('pinterest_url')->nullable()->after('youtube_url');
            }
            if (!Schema::hasColumn('footer_settings', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable()->after('pinterest_url');
            }
            if (!Schema::hasColumn('footer_settings', 'twitter_url')) {
                $table->string('twitter_url')->nullable()->after('linkedin_url');
            }
            if (!Schema::hasColumn('footer_settings', 'tiktok_url')) {
                $table->string('tiktok_url')->nullable()->after('twitter_url');
            }
            if (!Schema::hasColumn('footer_settings', 'column_1_title')) {
                $table->string('column_1_title')->nullable()->default('Information')->after('tiktok_url');
            }
            if (!Schema::hasColumn('footer_settings', 'column_1_links')) {
                $table->json('column_1_links')->nullable()->after('column_1_title');
            }
            if (!Schema::hasColumn('footer_settings', 'column_2_title')) {
                $table->string('column_2_title')->nullable()->default('Account')->after('column_1_links');
            }
            if (!Schema::hasColumn('footer_settings', 'column_2_links')) {
                $table->json('column_2_links')->nullable()->after('column_2_title');
            }
            if (!Schema::hasColumn('footer_settings', 'column_3_title')) {
                $table->string('column_3_title')->nullable()->default('Store')->after('column_2_links');
            }
            if (!Schema::hasColumn('footer_settings', 'column_3_links')) {
                $table->json('column_3_links')->nullable()->after('column_3_title');
            }
            if (!Schema::hasColumn('footer_settings', 'payment_methods')) {
                $table->json('payment_methods')->nullable()->after('column_3_links');
            }
        });
    }

    public function down(): void
    {
        Schema::table('footer_settings', function (Blueprint $table) {
            $columns = [
                'logo_image', 'address', 'map_url', 'working_hours_1', 'working_hours_2',
                'facebook_url', 'instagram_url', 'youtube_url', 'pinterest_url', 'linkedin_url', 'twitter_url', 'tiktok_url',
                'column_1_title', 'column_1_links', 'column_2_title', 'column_2_links', 'column_3_title', 'column_3_links',
                'payment_methods'
            ];
            $table->dropColumn($columns);
        });
    }
};
