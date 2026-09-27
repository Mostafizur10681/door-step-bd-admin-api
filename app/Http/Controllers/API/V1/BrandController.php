<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of active brands for the public website / store catalog.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Brand::where('status', 1);

        // Search by name, sub_title, or category_tag
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sub_title', 'like', "%{$s}%")
                  ->orWhere('category_tag', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        // Filter by category_tag / sector
        if ($request->filled('category_tag')) {
            $query->where('category_tag', $request->category_tag);
        }

        // Return all without pagination if all=true/1
        if ($request->boolean('all') || $request->input('all') === '1') {
            $brands = $query->latest('id')->get()->map(function ($brand) {
                return $this->formatBrand($brand);
            });

            return response()->json([
                'success' => true,
                'message' => 'Active brands retrieved successfully',
                'data' => $brands,
                'total' => $brands->count()
            ]);
        }

        $perPage = (int) $request->input('per_page', 20);
        $paginated = $query->latest('id')->paginate($perPage);

        $items = collect($paginated->items())->map(function ($brand) {
            return $this->formatBrand($brand);
        });

        return response()->json([
            'success' => true,
            'message' => 'Active brands retrieved successfully',
            'data' => $items,
            'pagination' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'has_more' => $paginated->hasMorePages(),
            ]
        ]);
    }

    /**
     * Display the specified active brand by ID or Slug.
     */
    public function show(string $identifier): JsonResponse
    {
        $brand = Brand::where('status', 1)
            ->where(function ($q) use ($identifier) {
                if (is_numeric($identifier)) {
                    $q->where('id', $identifier)->orWhere('slug', $identifier);
                } else {
                    $q->where('slug', $identifier);
                }
            })
            ->first();

        if (!$brand) {
            return $this->error('Brand not found or inactive', null, 404);
        }

        return $this->success($this->formatBrand($brand), 'Brand details retrieved successfully');
    }

    /**
     * Helper to format brand object with clean URLs and capability arrays.
     */
    protected function formatBrand(Brand $brand): array
    {
        $logo = $brand->logo;
        $logoUrl = null;

        if ($logo) {
            if (Str::startsWith($logo, ['http://', 'https://', 'data:image/'])) {
                $logoUrl = $logo;
            } else {
                $logoUrl = asset('storage/' . ltrim($logo, '/'));
            }
        }

        $capabilities = is_array($brand->key_capabilities) 
            ? $brand->key_capabilities 
            : (is_string($brand->key_capabilities) ? json_decode($brand->key_capabilities, true) : []);

        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            'logo' => $logo,
            'logo_url' => $logoUrl,
            'category_tag' => $brand->category_tag ?: 'HEAVY POWER GENERATION',
            'badge' => $brand->badge ?: 'Flagship Brand',
            'sub_title' => $brand->sub_title ?: 'GENERATORS & SYNCHRONIZATION',
            'description' => $brand->description,
            'capacity_range' => $brand->capacity_range ?: '50kVA - 3000kVA',
            'warranty_text' => $brand->warranty_text ?: 'Full Factory Warranty & AMC',
            'key_capabilities' => is_array($capabilities) ? array_values(array_filter($capabilities)) : [],
            'cta_text' => $brand->cta_text ?: 'Inquire About Brand',
            'cta_link' => $brand->cta_link ?: 'tel:01879198066',
            'status' => (bool) $brand->status,
            'created_at' => $brand->created_at ? $brand->created_at->toIso8601String() : null,
            'updated_at' => $brand->updated_at ? $brand->updated_at->toIso8601String() : null,
        ];
    }
}
