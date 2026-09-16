<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FAQCategory as FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::with('category');

        if ($request->filled('faq_category_id')) {
            $catName = FaqCategory::where('id', $request->faq_category_id)->value('name');
            if ($catName) {
                $query->where('category', $catName);
            }
        }

        $faqs = $query->latest()->paginate(15)->withQueryString();
        $categories = FaqCategory::all();

        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function create()
    {
        $categories = FaqCategory::all();
        return view('admin.faqs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $cat = FaqCategory::findOrFail($data['faq_category_id']);
        $data['category'] = $cat->name;
        $data['status'] = $data['status'] === 'active';
        unset($data['faq_category_id']);

        Faq::create($data);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ question created successfully!');
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        $categories = FaqCategory::all();
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);

        $data = $request->validate([
            'faq_category_id' => 'required|exists:faq_categories,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $cat = FaqCategory::findOrFail($data['faq_category_id']);
        $data['category'] = $cat->name;
        $data['status'] = $data['status'] === 'active';
        unset($data['faq_category_id']);

        $faq->update($data);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ question updated successfully!');
    }

    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ question deleted successfully!');
    }
}
