<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'name' => 'Sneakers', 'slug' => 'sneakers', 'status' => true],
            ['id' => 2, 'name' => 'Clothing', 'slug' => 'clothing', 'status' => true],
            ['id' => 3, 'name' => 'Shoe', 'slug' => 'shoe', 'status' => true],
            ['id' => 4, 'name' => 'Half Shoe', 'slug' => 'half-shoe', 'status' => true],
            ['id' => 5, 'name' => 'Ladies shoe', 'slug' => 'ladies-shoe', 'status' => true],
            ['id' => 6, 'name' => 'Ladies Loafer', 'slug' => 'ladies-loafer', 'status' => true],
            ['id' => 7, 'name' => 'Baby Shoe', 'slug' => 'baby-shoe', 'status' => true],
            ['id' => 8, 'name' => 'Jersey', 'slug' => 'jersey', 'status' => true],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['id' => $cat['id']],
                $cat
            );
        }
    }
}
