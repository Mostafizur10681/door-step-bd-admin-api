<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FAQCategory as FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FaqCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = FaqCategory::withCount('faqs')->latest()->paginate(15);
        return view('admin.faq-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.faq-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:faq_categories,name',
            'status' => 'required|in:active,inactive',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $data['status'] === 'active' ? 1 : 0;

        FaqCategory::create($data);

        return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category created successfully!');
    }

    public function edit($id)
    {
        $category = FaqCategory::findOrFail($id);
        return view('admin.faq-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = FaqCategory::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:faq_categories,name,' . $category->id,
            'status' => 'required|in:active,inactive',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['status'] = $data['status'] === 'active' ? 1 : 0;

        $category->update($data);

        return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category updated successfully!');
    }

    public function destroy($id)
    {
        $category = FaqCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.faq-categories.index')->with('success', 'FAQ Category deleted successfully!');
    }
}
