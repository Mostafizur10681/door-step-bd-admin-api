<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\ProductImage;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use UploadImageTrait;

    public function index(Request $request)
    {
        $query = Product::with(['category', 'subCategory', 'images']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                if (is_numeric($s)) {
                    $q->where('id', $s)
                      ->orWhere('name', 'like', "%{$s}%")
                      ->orWhere('sku', 'like', "%{$s}%")
                      ->orWhere('description', 'like', "%{$s}%");
                } else {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('sku', 'like', "%{$s}%")
                      ->orWhere('description', 'like', "%{$s}%");
                }
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('stock_status')) {
            $query->where('stock_status', $request->stock_status);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_low_high':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $brands = Brand::all();
        $attributes = Attribute::all();

        do {
            $generatedSku = 'SKU-' . strtoupper(Str::random(8));
        } while (Product::where('sku', $generatedSku)->exists());

        return view('admin.products.create', compact('categories', 'subCategories', 'brands', 'attributes', 'generatedSku'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'stock_status' => 'nullable|in:in-stock,out-of-stock,pre-order',
            'featured' => 'nullable|boolean',
            'best_seller' => 'nullable|boolean',
            'new_arrival' => 'nullable|boolean',
            'organic' => 'nullable|boolean',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'attributes' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ], [
            'images.*.max' => 'Each product image must not be larger than 10MB.',
            'images.*.image' => 'Each uploaded file must be a valid image.',
            'images.*.mimes' => 'Each image must be of type: jpeg, png, jpg, webp, gif.',
        ]);

        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        
        // Auto-generate unique SKU based on product name
        $baseSku = !empty($data['sku']) ? Str::upper(Str::slug($data['sku'])) : Str::upper(Str::slug($data['name']));
        if (empty($baseSku)) {
            $baseSku = 'SKU';
        }
        $baseSku = substr($baseSku, 0, 30);
        $sku = $baseSku . '-' . strtoupper(Str::random(4));
        while (Product::where('sku', $sku)->exists()) {
            $sku = $baseSku . '-' . strtoupper(Str::random(5));
        }
        $data['sku'] = $sku;
        
        unset($data['SKU']);
        $data['featured'] = $request->has('featured');
        $data['best_seller'] = $request->has('best_seller');
        $data['new_arrival'] = $request->has('new_arrival');
        $data['organic'] = $request->has('organic');

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $data['attributes'] = json_encode($data['attributes']);
        }

        $product = Product::create($data);

        // Process uploaded images
        if ($request->hasFile('images')) {
            $isPrimary = true;
            foreach ($request->file('images') as $file) {
                $path = $this->uploadImage($file, 'products');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => $isPrimary,
                ]);
                if ($isPrimary) {
                    $product->update(['image' => $path]);
                    $isPrimary = false;
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function show($id)
    {
        $product = Product::with(['category', 'subCategory', 'images'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with('images')->findOrFail($id);

        // If product has main image but no product_images records, ensure one is registered in gallery
        if (!empty($product->image) && $product->images->isEmpty()) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $product->image,
                'is_primary' => true,
            ]);
            $product->load('images');
        }
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $brands = Brand::all();
        $attributes = Attribute::all();

        return view('admin.products.edit', compact('product', 'categories', 'subCategories', 'brands', 'attributes'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'stock_status' => 'nullable|in:in-stock,out-of-stock,pre-order',
            'featured' => 'nullable|boolean',
            'best_seller' => 'nullable|boolean',
            'new_arrival' => 'nullable|boolean',
            'organic' => 'nullable|boolean',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'additional_info' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'attributes' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ], [
            'images.*.max' => 'Each product image must not be larger than 10MB.',
            'images.*.image' => 'Each uploaded file must be a valid image.',
            'images.*.mimes' => 'Each image must be of type: jpeg, png, jpg, webp, gif.',
        ]);

        // Auto-generate unique SKU based on product name if needed
        $baseSku = !empty($data['sku']) ? Str::upper(Str::slug($data['sku'])) : Str::upper(Str::slug($data['name']));
        if (empty($baseSku)) {
            $baseSku = 'SKU';
        }
        $baseSku = substr($baseSku, 0, 30);
        $sku = $baseSku . '-' . strtoupper(Str::random(4));
        while (Product::where('sku', $sku)->where('id', '!=', $product->id)->exists()) {
            $sku = $baseSku . '-' . strtoupper(Str::random(5));
        }
        $data['sku'] = $sku;

        unset($data['SKU']);
        $data['featured'] = $request->has('featured');
        $data['best_seller'] = $request->has('best_seller');
        $data['new_arrival'] = $request->has('new_arrival');
        $data['organic'] = $request->has('organic');

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $data['attributes'] = json_encode($data['attributes']);
        }

        $product->update($data);

        // Upload additional images if provided
        if ($request->hasFile('images')) {
            $hasPrimary = !empty($product->image);
            foreach ($request->file('images') as $file) {
                $path = $this->uploadImage($file, 'products');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => !$hasPrimary,
                ]);
                if (!$hasPrimary) {
                    $product->update(['image' => $path]);
                    $hasPrimary = true;
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        foreach ($product->images as $img) {
            $this->deleteImage($img->image_path);
            $img->delete();
        }

        if ($product->image) {
            $this->deleteImage($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    public function deleteImageItem($imageId)
    {
        $image = ProductImage::findOrFail($imageId);
        $this->deleteImage($image->image_path);
        $productId = $image->product_id;
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Image removed']);
    }

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();

        return back()->with('success', 'Product status updated to ' . $product->status);
    }

    public function setPrimaryImage($imageId)
    {
        $targetImage = ProductImage::findOrFail($imageId);
        $productId = $targetImage->product_id;

        ProductImage::where('product_id', $productId)->update(['is_primary' => false]);
        $targetImage->update(['is_primary' => true]);

        Product::where('id', $productId)->update(['image' => $targetImage->image_path]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image updated successfully',
            'image_url' => $targetImage->image_path
        ]);
    }

}
