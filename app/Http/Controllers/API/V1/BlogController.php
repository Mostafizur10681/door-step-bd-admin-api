<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Get paginated published blogs for public frontend (e.g. Next.js / React at http://localhost:3000/blog)
     */
    public function index(Request $request)
    {
        $query = Blog::with('category')->where('status', 'published');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                   ->orWhere('short_description', 'like', "%{$s}%")
                   ->orWhere('content', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $catSlug = $request->category;
            $query->whereHas('category', function ($q) use ($catSlug) {
                $q->where('slug', $catSlug);
            });
        }

        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        $blogs = $query->latest('published_at')->paginate($request->input('per_page', 9));

        return response()->json([
            'success' => true,
            'data'    => $blogs->items(),
            'pagination' => [
                'total'        => $blogs->total(),
                'per_page'     => $blogs->perPage(),
                'current_page' => $blogs->currentPage(),
                'last_page'    => $blogs->lastPage(),
            ]
        ]);
    }

    /**
     * Get single blog details by ID or Slug.
     */
    public function show($identifier)
    {
        $blog = Blog::with(['category', 'approvedComments'])
            ->where('status', 'published')
            ->where(function ($q) use ($identifier) {
                if (is_numeric($identifier)) {
                    $q->where('id', $identifier);
                }
                $q->orWhere('slug', $identifier);
            })
            ->firstOrFail();

        // Increment views count safely
        $blog->increment('views');

        // Fetch recent/related blogs
        $relatedBlogs = Blog::where('status', 'published')
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(3)
            ->get();

        return response()->json([
            'success'       => true,
            'data'          => $blog,
            'related_blogs' => $relatedBlogs,
        ]);
    }

    /**
     * Get active blog categories.
     */
    public function categories()
    {
        $categories = BlogCategory::where('status', true)->withCount(['blogs' => function($q) {
            $q->where('status', 'published');
        }])->get();

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    /**
     * Store public blog comment.
     */
    public function storeComment(Request $request, $slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:255',
            'comment' => 'required|string|max:1000',
        ]);

        $data['blog_id'] = $blog->id;
        $data['status']  = 'pending';

        $comment = BlogComment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Your comment has been submitted and is awaiting moderation.',
            'data'    => $comment,
        ]);
    }
}
