<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    use UploadImageTrait;

    public function index(Request $request)
    {
        $query = SubCategory::with('category')->withCount('products');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $subCategories = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.sub-categories.index', compact('subCategories', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.sub-categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'is_active' => 'nullable',
            'status' => 'nullable',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $request->has('status') 
            ? $request->boolean('status') 
            : ($request->has('is_active') ? true : false);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'sub-categories');
        }

        SubCategory::create($data);

        return redirect()->route('admin.sub-categories.index')->with('success', 'Sub category created successfully!');
    }

    public function edit($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $categories = Category::all();
        return view('admin.sub-categories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'is_active' => 'nullable',
            'status' => 'nullable',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $request->has('status') 
            ? $request->boolean('status') 
            : ($request->has('is_active') ? true : false);

        if ($request->hasFile('image')) {
            if ($subCategory->image) {
                $this->deleteImage($subCategory->image);
            }
            $data['image'] = $this->uploadImage($request->file('image'), 'sub-categories');
        }

        $subCategory->update($data);

        return redirect()->route('admin.sub-categories.index')->with('success', 'Sub category updated successfully!');
    }

    public function destroy($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        if ($subCategory->image) {
            $this->deleteImage($subCategory->image);
        }
        $subCategory->delete();

        return redirect()->route('admin.sub-categories.index')->with('success', 'Sub category deleted successfully!');
    }
}
