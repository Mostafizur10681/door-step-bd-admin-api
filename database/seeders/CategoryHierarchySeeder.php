<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $men = Category::firstOrCreate(['slug' => 'men'], ['name' => 'Men', 'status' => true]);
        $women = Category::firstOrCreate(['slug' => 'women'], ['name' => 'Women', 'status' => true]);
        $children = Category::firstOrCreate(['slug' => 'children'], ['name' => 'Children', 'status' => true]);

        // Assign sub-categories under main categories
        Category::whereIn('id', [1, 3, 4, 8])->update(['parent_id' => $men->id]);
        Category::whereIn('id', [5, 6])->update(['parent_id' => $women->id]);
        Category::where('id', 7)->update(['parent_id' => $children->id]);

        $subCats = Category::whereNotNull('parent_id')->get();
        foreach ($subCats as $sc) {
            \App\Models\SubCategory::firstOrCreate([
                'category_id' => $sc->parent_id,
                'name' => $sc->name
            ], [
                'slug' => \Illuminate\Support\Str::slug($sc->name),
                'description' => $sc->description,
                'image' => $sc->image,
                'status' => $sc->status
            ]);
        }
    }
}
