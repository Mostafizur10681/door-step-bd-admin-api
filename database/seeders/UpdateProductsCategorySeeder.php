<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class UpdateProductsCategorySeeder extends Seeder
{
    public function run(): void
    {
        $men = Category::where('name', 'Men')->first();
        $women = Category::where('name', 'Women')->first();
        $children = Category::where('name', 'Children')->first();

        $subCats = \App\Models\SubCategory::all()->keyBy('name');

        $allProds = Product::all();
        foreach ($allProds as $p) {
            $sub = strtolower($p->sub_category ?? '');
            $name = strtolower($p->name ?? '');

            if (str_contains($sub, 'sneaker') || str_contains($name, 'sneaker')) {
                if ($men) $p->category_id = $men->id;
                $p->sub_category = 'Sneakers';
                $p->sub_category_id = $subCats->get('Sneakers')?->id;
            } elseif (str_contains($sub, 'ladies') || str_contains($name, 'ladies') || str_contains($name, 'women')) {
                if ($women) $p->category_id = $women->id;
                $subName = str_contains($sub, 'loafer') ? 'Ladies Loafer' : 'Ladies shoe';
                $p->sub_category = $subName;
                $p->sub_category_id = $subCats->get($subName)?->id;
            } elseif (str_contains($sub, 'baby') || str_contains($name, 'baby') || str_contains($name, 'child') || str_contains($name, 'kid')) {
                if ($children) $p->category_id = $children->id;
                $p->sub_category = 'Baby Shoe';
                $p->sub_category_id = $subCats->get('Baby Shoe')?->id;
            } else {
                if ($men) $p->category_id = $men->id;
                if (empty($p->sub_category)) {
                    $p->sub_category = 'Shoe';
                }
                $p->sub_category_id = $subCats->get($p->sub_category)?->id;
            }
            $p->save();
        }
    }
}
