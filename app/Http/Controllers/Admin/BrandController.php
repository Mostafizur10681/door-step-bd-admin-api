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
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 1 : 0);
        }

        $perPage = (int) $request->input('per_page', 18);
        $brands = $query->latest('id')->paginate($perPage)->withQueryString();

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
     * Only inserts brand name, img (logo), description, and status.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'description' => 'nullable|string',
            'logo' => $request->hasFile('logo') ? 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif,avif|max:10240' : 'nullable|string',
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

        // Normalize status
        if ($request->has('status')) {
            $data['status'] = in_array($request->status, [1, '1', 'active', true], true) ? 1 : 0;
        } else {
            $data['status'] = $request->has('is_active') ? 1 : 0;
        }

        // Handle logo / image upload
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
            'description' => 'nullable|string',
            'logo' => $request->hasFile('logo') ? 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif,avif|max:10240' : 'nullable|string',
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

        // Normalize status
        if ($request->has('status')) {
            $data['status'] = in_array($request->status, [1, '1', 'active', true], true) ? 1 : 0;
        } else {
            $data['status'] = $request->has('is_active') ? 1 : 0;
        }

        // Handle image update
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
