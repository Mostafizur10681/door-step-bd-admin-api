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
            if (!Schema::hasColumn('contact_settings', 'whatsapp_is_enabled')) {
                $table->boolean('whatsapp_is_enabled')->default(true)->after('whatsapp_number');
            }
            if (!Schema::hasColumn('contact_settings', 'whatsapp_default_message')) {
                $table->text('whatsapp_default_message')->nullable()->after('whatsapp_is_enabled');
            }
            if (!Schema::hasColumn('contact_settings', 'whatsapp_position')) {
                $table->string('whatsapp_position')->default('bottom-right')->after('whatsapp_default_message');
            }
            if (!Schema::hasColumn('contact_settings', 'whatsapp_header_title')) {
                $table->string('whatsapp_header_title')->nullable()->after('whatsapp_position');
            }
            if (!Schema::hasColumn('contact_settings', 'whatsapp_header_subtitle')) {
                $table->string('whatsapp_header_subtitle')->nullable()->after('whatsapp_header_title');
            }
            if (!Schema::hasColumn('contact_settings', 'whatsapp_button_text')) {
                $table->string('whatsapp_button_text')->nullable()->after('whatsapp_header_subtitle');
            }
            if (!Schema::hasColumn('contact_settings', 'whatsapp_show_floating_button')) {
                $table->boolean('whatsapp_show_floating_button')->default(true)->after('whatsapp_button_text');
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
                'whatsapp_is_enabled',
                'whatsapp_default_message',
                'whatsapp_position',
                'whatsapp_header_title',
                'whatsapp_header_subtitle',
                'whatsapp_button_text',
                'whatsapp_show_floating_button',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('contact_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
