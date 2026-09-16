<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // Divisions
    public function divisions(Request $request)
    {
        $divisions = Division::withCount('districts')->latest()->paginate(15);
        return view('admin.locations.divisions', compact('divisions'));
    }

    public function storeDivision(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:divisions,name',
            'bn_name' => 'nullable|string|max:255',
        ]);

        Division::create($data);

        return redirect()->route('admin.locations.divisions')->with('success', 'Division created successfully!');
    }

    public function destroyDivision($id)
    {
        $div = Division::findOrFail($id);
        $div->delete();

        return redirect()->route('admin.locations.divisions')->with('success', 'Division deleted successfully!');
    }

    // Districts
    public function districts(Request $request)
    {
        $query = District::with('division')->withCount('thanas');
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        $districts = $query->latest()->paginate(15)->withQueryString();
        $divisions = Division::all();

        return view('admin.locations.districts', compact('districts', 'divisions'));
    }

    public function storeDistrict(Request $request)
    {
        $data = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'bn_name' => 'nullable|string|max:255',
        ]);

        District::create($data);

        return redirect()->route('admin.locations.districts')->with('success', 'District created successfully!');
    }

    public function destroyDistrict($id)
    {
        $dist = District::findOrFail($id);
        $dist->delete();

        return redirect()->route('admin.locations.districts')->with('success', 'District deleted successfully!');
    }

    // Thanas
    public function thanas(Request $request)
    {
        $query = Thana::with('district.division');
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        $thanas = $query->latest()->paginate(15)->withQueryString();
        $districts = District::all();

        return view('admin.locations.thanas', compact('thanas', 'districts'));
    }

    public function storeThana(Request $request)
    {
        $data = $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name' => 'required|string|max:255',
            'bn_name' => 'nullable|string|max:255',
        ]);

        Thana::create($data);

        return redirect()->route('admin.locations.thanas')->with('success', 'Thana / Upazila created successfully!');
    }

    public function destroyThana($id)
    {
        $thana = Thana::findOrFail($id);
        $thana->delete();

        return redirect()->route('admin.locations.thanas')->with('success', 'Thana deleted successfully!');
    }
}
