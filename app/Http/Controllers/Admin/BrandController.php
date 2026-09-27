<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    use UploadImageTrait;

    /**
     * Display a listing of brands.
     */
    public function index(Request $request)
    {
        $query = Brand::query();

        // Search filter
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 1 : 0);
        }

        $perPage = (int) $request->input('per_page', 15);
        $brands = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Brand::count(),
            'active' => Brand::where('status', 1)->count(),
            'inactive' => Brand::where('status', 0)->count(),
        ];

        return view('admin.brands.index', compact('brands', 'stats'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'category_tag' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity_range' => 'nullable|string|max:255',
            'warranty_text' => 'nullable|string|max:255',
            'key_capabilities' => 'nullable',
            'cta_text' => 'nullable|string|max:255',
            'cta_link' => 'nullable|string|max:255',
            'logo' => $request->hasFile('logo') ? 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif|max:10240' : 'nullable|string',
            'status' => 'nullable|in:0,1,active,inactive',
        ]);

        $data['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        
        // Ensure slug uniqueness fallback
        $baseSlug = $data['slug'];
        $count = 1;
        while (Brand::where('slug', $data['slug'])->exists()) {
            $data['slug'] = "{$baseSlug}-{$count}";
            $count++;
        }

        // Normalize key capabilities from string or array
        if ($request->filled('key_capabilities')) {
            if (is_string($request->key_capabilities)) {
                $caps = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->key_capabilities))));
                $data['key_capabilities'] = array_values($caps);
            } elseif (is_array($request->key_capabilities)) {
                $data['key_capabilities'] = array_values(array_filter($request->key_capabilities));
            }
        } else {
            $data['key_capabilities'] = [];
        }

        // Normalize status
        if ($request->has('status')) {
            $data['status'] = in_array($request->status, [1, '1', 'active', true], true) ? 1 : 0;
        } else {
            $data['status'] = $request->has('is_active') ? 1 : 0;
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadImage($request->file('logo'), 'brands');
        } elseif ($request->filled('logo') && is_string($request->logo)) {
            if (Str::startsWith($request->logo, 'data:image/')) {
                $data['logo'] = $this->uploadBase64Image($request->logo, 'brands');
            } else {
                $data['logo'] = $request->logo;
            }
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created successfully!');
    }

    /**
     * Display the specified brand.
     */
    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'category_tag' => 'nullable|string|max:255',
            'badge' => 'nullable|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity_range' => 'nullable|string|max:255',
            'warranty_text' => 'nullable|string|max:255',
            'key_capabilities' => 'nullable',
            'cta_text' => 'nullable|string|max:255',
            'cta_link' => 'nullable|string|max:255',
            'logo' => $request->hasFile('logo') ? 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif|max:10240' : 'nullable|string',
            'status' => 'nullable|in:0,1,active,inactive',
        ]);

        $data['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);

        // Ensure slug uniqueness excluding current
        $baseSlug = $data['slug'];
        $count = 1;
        while (Brand::where('slug', $data['slug'])->where('id', '!=', $brand->id)->exists()) {
            $data['slug'] = "{$baseSlug}-{$count}";
            $count++;
        }

        // Normalize key capabilities
        if ($request->has('key_capabilities')) {
            if (is_string($request->key_capabilities)) {
                $caps = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->key_capabilities))));
                $data['key_capabilities'] = array_values($caps);
            } elseif (is_array($request->key_capabilities)) {
                $data['key_capabilities'] = array_values(array_filter($request->key_capabilities));
            } else {
                $data['key_capabilities'] = [];
            }
        }

        // Normalize status
        if ($request->has('status')) {
            $data['status'] = in_array($request->status, [1, '1', 'active', true], true) ? 1 : 0;
        } else {
            $data['status'] = $request->has('is_active') ? 1 : 0;
        }

        if ($request->hasFile('logo')) {
            if ($brand->logo) {
                $this->deleteImage($brand->logo);
            }
            $data['logo'] = $this->uploadImage($request->file('logo'), 'brands');
        } elseif ($request->filled('logo') && is_string($request->logo)) {
            if (Str::startsWith($request->logo, 'data:image/')) {
                if ($brand->logo) {
                    $this->deleteImage($brand->logo);
                }
                $data['logo'] = $this->uploadBase64Image($request->logo, 'brands');
            } else {
                $data['logo'] = $request->logo;
            }
        } else {
            unset($data['logo']);
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated successfully!');
    }

    /**
     * Toggle status between active and inactive.
     */
    public function toggleStatus($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->status = !$brand->status;
        $brand->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $brand->status,
                'message' => 'Brand status updated to ' . ($brand->status ? 'Active' : 'Inactive')
            ]);
        }

        return redirect()->back()->with('success', 'Brand status updated to ' . ($brand->status ? 'Active' : 'Inactive'));
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        if ($brand->logo) {
            $this->deleteImage($brand->logo);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted successfully!');
    }
}
