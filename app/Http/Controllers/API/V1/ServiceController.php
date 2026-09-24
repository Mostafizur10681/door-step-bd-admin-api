<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get active services for public website
     */
    public function index(Request $request)
    {
        $query = Service::where('status', 'active');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('short_description', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $perPage = $request->input('per_page', 12);
        $services = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $services->items(),
            'pagination' => [
                'total'        => $services->total(),
                'per_page'     => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page'    => $services->lastPage(),
            ]
        ]);
    }

    /**
     * Get single service by ID or Slug
     */
    public function show($identifier)
    {
        $service = Service::where('status', 'active')
            ->where(function ($q) use ($identifier) {
                $q->where('slug', $identifier)
                  ->orWhere('id', is_numeric($identifier) ? $identifier : 0);
            })
            ->first();

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $service
        ]);
    }
}
