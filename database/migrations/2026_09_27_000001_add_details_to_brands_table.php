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
        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'category_tag')) {
                $table->string('category_tag')->nullable()->after('name');
            }
            if (!Schema::hasColumn('brands', 'badge')) {
                $table->string('badge')->nullable()->after('category_tag');
            }
            if (!Schema::hasColumn('brands', 'sub_title')) {
                $table->string('sub_title')->nullable()->after('badge');
            }
            if (!Schema::hasColumn('brands', 'description')) {
                $table->text('description')->nullable()->after('sub_title');
            }
            if (!Schema::hasColumn('brands', 'capacity_range')) {
                $table->string('capacity_range')->nullable()->after('description');
            }
            if (!Schema::hasColumn('brands', 'warranty_text')) {
                $table->string('warranty_text')->nullable()->after('capacity_range');
            }
            if (!Schema::hasColumn('brands', 'key_capabilities')) {
                $table->json('key_capabilities')->nullable()->after('warranty_text');
            }
            if (!Schema::hasColumn('brands', 'cta_text')) {
                $table->string('cta_text')->nullable()->after('key_capabilities');
            }
            if (!Schema::hasColumn('brands', 'cta_link')) {
                $table->string('cta_link')->nullable()->after('cta_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn([
                'category_tag',
                'badge',
                'sub_title',
                'description',
                'capacity_range',
                'warranty_text',
                'key_capabilities',
                'cta_text',
                'cta_link',
            ]);
        });
    }
};
