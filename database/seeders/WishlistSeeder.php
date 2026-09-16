<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        // Get customer user IDs
        $customerUserIds = DB::table('users')
            ->where('role', 'customer')
            ->pluck('id');

        if ($customerUserIds->isEmpty()) {
            // Fallback to any non-admin users or first user
            $customerUserIds = DB::table('users')->pluck('id');
        }

        // Get product IDs
        $productIds = DB::table('products')->pluck('id')->take(10);

        if ($customerUserIds->isEmpty() || $productIds->isEmpty()) {
            return;
        }

        // Seed 2-3 wishlist items per user
        foreach ($customerUserIds as $userId) {
            // Pick 3 random products for each customer
            $randomProducts = $productIds->random(min(3, $productIds->count()));

            foreach ($randomProducts as $productId) {
                DB::table('wishlists')->updateOrInsert([
                    'user_id' => $userId,
                    'product_id' => $productId,
                ], [
                    'created_at' => now()->subDays(rand(1, 15)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
