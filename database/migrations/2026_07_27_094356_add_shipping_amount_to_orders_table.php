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
        if (Schema::hasColumn('orders', 'shipping_amount')) { return; }

                Schema::table('orders', function (Blueprint $table) {
            $table->decimal('shipping_amount', 10, 2)->default(0.00)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('shipping_amount');
        });
    }
};
