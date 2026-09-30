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
     * Display a listing of active brands for website / store catalog.
     * Website shows brand logo/image, name, and description.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Brand::where('status', 1);

        // Search by name or description
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        // Return all without pagination if all=true/1 or limit/all query
        if ($request->boolean('all') || $request->input('all') === '1' || $request->input('limit') === 'all') {
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

        $perPage = (int) $request->input('per_page', 30);
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
     * Helper to format brand object with clean URLs.
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

        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            'logo' => $logo,
            'logo_url' => $logoUrl,
            'image' => $logoUrl,
            'description' => $brand->description,
            'status' => (bool) $brand->status,
            'created_at' => $brand->created_at ? $brand->created_at->toIso8601String() : null,
            'updated_at' => $brand->updated_at ? $brand->updated_at->toIso8601String() : null,
        ];
    }
}
