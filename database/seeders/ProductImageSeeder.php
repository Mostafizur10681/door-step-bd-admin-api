<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch an existing base64 image to duplicate or use a clean sample base64 string
        $existingImage = DB::table('product_images')->whereNotNull('image_path')->value('image_path');

        if (!$existingImage) {
            // A clean 1x1 placeholder base64 JPEG image data URI
            $existingImage = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        }

        // Get all product IDs from the products table
        $productIds = DB::table('products')->pluck('id');

        foreach ($productIds as $productId) {
            // Check if product already has an image in product_images table
            $hasImage = DB::table('product_images')->where('product_id', $productId)->exists();

            if (!$hasImage) {
                DB::table('product_images')->insert([
                    'product_id' => $productId,
                    'image_path' => $existingImage,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
