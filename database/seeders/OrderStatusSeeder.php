<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Pending',
                'slug' => 'pending',
                'description' => 'Order has been placed and awaiting verification',
                'status' => true,
            ],
            [
                'name' => 'Confirmed',
                'slug' => 'confirmed',
                'description' => 'Order verified and accepted by store',
                'status' => true,
            ],
            [
                'name' => 'Processing',
                'slug' => 'processing',
                'description' => 'Items packed and prepared for logistics dispatch',
                'status' => true,
            ],
            [
                'name' => 'Shipped',
                'slug' => 'shipped',
                'description' => 'Parcel handed over to courier partner for delivery',
                'status' => true,
            ],
            [
                'name' => 'Out for Delivery',
                'slug' => 'out-for-delivery',
                'description' => 'Parcel is out with delivery agent in destination area',
                'status' => true,
            ],
            [
                'name' => 'Delivered',
                'slug' => 'delivered',
                'description' => 'Parcel received and completed',
                'status' => true,
            ],
            [
                'name' => 'Cancelled',
                'slug' => 'cancelled',
                'description' => 'Order cancelled by customer or store',
                'status' => true,
            ],
            [
                'name' => 'Returned',
                'slug' => 'returned',
                'description' => 'Parcel returned to warehouse',
                'status' => true,
            ],
        ];

        foreach ($statuses as $st) {
            OrderStatus::updateOrCreate(
                ['slug' => $st['slug']],
                $st
            );
        }
    }
}
