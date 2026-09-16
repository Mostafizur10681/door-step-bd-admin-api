
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'company_name')) {
                $table->string('company_name')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('orders', 'country')) {
                $table->string('country')->nullable()->default('Bangladesh')->after('customer_email');
            }
            if (!Schema::hasColumn('orders', 'town_city')) {
                $table->string('town_city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('orders', 'postcode')) {
                $table->string('postcode')->nullable()->after('town_city');
            }
            if (!Schema::hasColumn('orders', 'order_notes')) {
                $table->text('order_notes')->nullable()->after('postcode');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable()->default('cod')->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'ship_different')) {
                $table->boolean('ship_different')->default(false)->after('order_notes');
            }
            if (!Schema::hasColumn('orders', 'ship_customer_name')) {
                $table->string('ship_customer_name')->nullable()->after('ship_different');
            }
            if (!Schema::hasColumn('orders', 'ship_company_name')) {
                $table->string('ship_company_name')->nullable()->after('ship_customer_name');
            }
            if (!Schema::hasColumn('orders', 'ship_country')) {
                $table->string('ship_country')->nullable()->after('ship_company_name');
            }
            if (!Schema::hasColumn('orders', 'ship_address')) {
                $table->text('ship_address')->nullable()->after('ship_country');
            }
            if (!Schema::hasColumn('orders', 'ship_town_city')) {
                $table->string('ship_town_city')->nullable()->after('ship_address');
            }
            if (!Schema::hasColumn('orders', 'ship_postcode')) {
                $table->string('ship_postcode')->nullable()->after('ship_town_city');
            }
            if (!Schema::hasColumn('orders', 'ship_district')) {
                $table->string('ship_district')->nullable()->after('ship_postcode');
            }
            if (!Schema::hasColumn('orders', 'ship_phone')) {
                $table->string('ship_phone')->nullable()->after('ship_district');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'country',
                'town_city',
                'postcode',
                'order_notes',
                'payment_method',
                'ship_different',
                'ship_customer_name',
                'ship_company_name',
                'ship_country',
                'ship_address',
                'ship_town_city',
                'ship_postcode',
                'ship_district',
                'ship_phone',
            ]);
        });
    }
};
