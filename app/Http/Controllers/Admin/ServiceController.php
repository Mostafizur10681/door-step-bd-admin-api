<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use UploadImageTrait;

    public function index(Request $request)
    {
        $query = Service::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('short_description', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 12);
        $services = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Service::count(),
            'active' => Service::where('status', 'active')->count(),
            'inactive' => Service::where('status', 'inactive')->count(),
        ];

        return view('admin.services.index', compact('services', 'stats'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image_file')) {
            $data['image'] = $this->uploadImage($request->file('image_file'), 'services');
        } elseif (!empty($data['image']) && str_starts_with($data['image'], 'data:image')) {
            $data['image'] = $this->uploadBase64Image($data['image'], 'services');
        }

        unset($data['image_file']);

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully!');
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:services,slug,' . $service->id,
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('image_file')) {
            $this->deleteImage($service->image);
            $data['image'] = $this->uploadImage($request->file('image_file'), 'services');
        } elseif (!empty($data['image']) && str_starts_with($data['image'], 'data:image')) {
            $data['image'] = $this->uploadBase64Image($data['image'], 'services');
        }

        unset($data['image_file']);

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully!');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        if ($service->image) {
            $this->deleteImage($service->image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->status = $service->status === 'active' ? 'inactive' : 'active';
        $service->save();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $service->status,
                'message' => 'Service status updated to ' . $service->status
            ]);
        }

        return back()->with('success', 'Service status updated to ' . $service->status);
    }
}
