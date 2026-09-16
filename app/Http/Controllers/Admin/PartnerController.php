<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    use UploadImageTrait;

    public function index(Request $request)
    {
        $query = Partner::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%")->orWhere('website', 'like', "%{$s}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 1 : 0);
        }

        $perPage = $request->input('per_page', 12);
        $partners = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total' => Partner::count(),
            'active' => Partner::where('status', 1)->count(),
            'inactive' => Partner::where('status', 0)->count(),
        ];

        return view('admin.partners.index', compact('partners', 'stats'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'status' => 'required|in:active,inactive',
        ]);

        $data['status'] = $data['status'] === 'active' ? 1 : 0;

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->uploadImage($request->file('logo'), 'partners');
        }

        Partner::create($data);

        return redirect()->route('admin.partners.index')->with('success', 'Partner created successfully!');
    }

    public function edit($id)
    {
        $partner = Partner::findOrFail($id);
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'status' => 'required|in:active,inactive',
        ]);

        $data['status'] = $data['status'] === 'active' ? 1 : 0;

        if ($request->hasFile('logo')) {
            if ($partner->logo) {
                $this->deleteImage($partner->logo);
            }
            $data['logo'] = $this->uploadImage($request->file('logo'), 'partners');
        }

        $partner->update($data);

        return redirect()->route('admin.partners.index')->with('success', 'Partner updated successfully!');
    }

    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        if ($partner->logo) {
            $this->deleteImage($partner->logo);
        }
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Partner deleted successfully!');
    }
}
