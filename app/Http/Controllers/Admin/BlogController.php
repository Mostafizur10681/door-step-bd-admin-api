<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['category', 'user'])->withCount('comments');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                   ->orWhere('short_description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('blog_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $blogs = $query->latest()->paginate($request->input('per_page', 15))->withQueryString();
        $categories = BlogCategory::where('status', true)->get();

        $stats = [
            'total'     => Blog::count(),
            'published' => Blog::where('status', 'published')->count(),
            'draft'     => Blog::where('status', 'draft')->count(),
            'featured'  => Blog::where('featured', true)->count(),
        ];

        return view('admin.blogs.index', compact('blogs', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = BlogCategory::where('status', true)->get();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'blog_category_id'  => 'nullable|exists:blog_categories,id',
            'author_name'       => 'nullable|string|max:100',
            'image'             => 'nullable|string',
            'short_description' => 'nullable|string',
            'content'           => 'required|string',
            'status'            => 'required|in:published,draft',
            'featured'          => 'nullable|boolean',
            'published_at'      => 'nullable|date',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string',
            'meta_keywords'     => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();
        $data['author_name'] = $data['author_name'] ?: (Auth::user()->name ?? 'Admin');
        $data['featured'] = $request->has('featured');
        $data['published_at'] = $data['published_at'] ?? now();

        $blog = Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully!');
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::where('status', true)->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'blog_category_id'  => 'nullable|exists:blog_categories,id',
            'author_name'       => 'nullable|string|max:100',
            'image'             => 'nullable|string',
            'short_description' => 'nullable|string',
            'content'           => 'required|string',
            'status'            => 'required|in:published,draft',
            'featured'          => 'nullable|boolean',
            'published_at'      => 'nullable|date',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string',
            'meta_keywords'     => 'nullable|string',
        ]);

        $data['featured'] = $request->has('featured');

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully!');
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully!');
    }

    // Blog Categories Management
    public function categories()
    {
        $categories = BlogCategory::withCount('blogs')->latest()->get();
        return view('admin.blogs.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:blog_categories,name',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        BlogCategory::create($data);

        return redirect()->route('admin.blogs.categories')->with('success', 'Blog category created successfully!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255|unique:blog_categories,name,' . $id,
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        $category->update($data);

        return redirect()->route('admin.blogs.categories')->with('success', 'Blog category updated successfully!');
    }

    public function destroyCategory($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.blogs.categories')->with('success', 'Blog category deleted successfully!');
    }

    // Blog Comments Moderation
    public function comments(Request $request)
    {
        $query = BlogComment::with('blog');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $comments = $query->latest()->paginate(15);
        return view('admin.blogs.comments', compact('comments'));
    }

    public function approveComment($id)
    {
        $comment = BlogComment::findOrFail($id);
        $comment->update(['status' => 'approved']);

        return back()->with('success', 'Comment approved!');
    }

    public function destroyComment($id)
    {
        $comment = BlogComment::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Comment deleted!');
    }
}
