<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        $query = Attribute::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%");
        }

        $attributes = $query->latest()->paginate(15)->withQueryString();

        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'values' => 'required|array|min:1',
            'values.*' => 'required|string|max:255',
        ]);

        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Attribute::where('code', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $data['code'] = $slug;
        $data['type'] = 'text';
        // Ensure values are stored cleanly as array
        $data['values'] = array_values(array_unique(array_filter(array_map('trim', $data['values']))));

        Attribute::create($data);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute created successfully!');
    }

    public function edit($id)
    {
        $attribute = Attribute::findOrFail($id);
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $attribute = Attribute::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'values' => 'required|array|min:1',
            'values.*' => 'required|string|max:255',
        ]);

        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $count = 1;
        while (Attribute::where('code', $slug)->where('id', '!=', $attribute->id)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $data['code'] = $slug;
        $data['type'] = 'text';
        $data['values'] = array_values(array_unique(array_filter(array_map('trim', $data['values']))));

        $attribute->update($data);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully!');
    }

    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted successfully!');
    }
}
