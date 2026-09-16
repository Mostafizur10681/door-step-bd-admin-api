<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wishlists') && !Schema::hasColumn('wishlists', 'last_notified_at')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->timestamp('last_notified_at')->nullable()->after('product_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('wishlists') && Schema::hasColumn('wishlists', 'last_notified_at')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->dropColumn('last_notified_at');
            });
        }
    }
};
