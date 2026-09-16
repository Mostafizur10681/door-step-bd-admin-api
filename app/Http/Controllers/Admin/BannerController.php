<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    use UploadImageTrait;

    public const MENU_LOCATIONS = [
        'home_hero_slider' => 'Homepage Hero Main Slider',
        'home_middle_banner' => 'Homepage Middle / Promo Banner',
        'home_popup' => 'Homepage Promotional Popup',
        'category_banner' => 'Category Page Header Banner',
    ];

    public function index(Request $request)
    {
        $query = Banner::query();

        // Status filter
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('menu_location', $request->input('location'));
        }

        // Search query filter
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_line1', 'like', "%{$search}%")
                  ->orWhere('title_line2', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('badge', 'like', "%{$search}%")
                  ->orWhere('cta_text', 'like', "%{$search}%")
                  ->orWhere('cta_link', 'like', "%{$search}%");
            });
        }

        $banners = $query->orderBy('order', 'asc')->orderBy('id', 'asc')->paginate(12)->withQueryString();
        $totalCount = Banner::count();
        $activeCount = Banner::where('is_active', true)->count();
        $inactiveCount = Banner::where('is_active', false)->count();
        $menuLocations = self::MENU_LOCATIONS;

        return view('admin.banners.index', compact('banners', 'totalCount', 'activeCount', 'inactiveCount', 'menuLocations'));
    }

    public function create()
    {
        $nextOrder = (Banner::max('order') ?? 0) + 1;
        $menuLocations = self::MENU_LOCATIONS;
        return view('admin.banners.create', compact('nextOrder', 'menuLocations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'badge' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'title_line1' => 'nullable|string|max:255',
            'title_line2' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'discount_text' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'bg_color' => 'nullable|string|max:50',
            'right_bg_color' => 'nullable|string|max:50',
            'menu_location' => 'nullable|string|max:100',
            'is_active' => 'nullable',
            'image' => 'nullable',
            'image_base64' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
            'mobile_image' => 'nullable',
            'mobile_image_base64' => 'nullable|string',
            'mobile_image_url' => 'nullable|string|max:500',
            'left_image' => 'nullable',
            'left_image_base64' => 'nullable|string',
            'left_image_url' => 'nullable|string|max:500',
        ]);

        $titleLine1 = trim((string) ($request->input('title_line1') ?? ''));
        $titleLine2 = trim((string) ($request->input('title_line2') ?? ''));
        $title = trim((string) ($request->input('title') ?? ''));

        if (empty($title)) {
            $title = trim($titleLine1 . ' ' . $titleLine2);
        }

        $badge = $request->input('badge') ?: $request->input('tagline') ?: null;
        $subtitle = $request->input('subtitle') ?: $request->input('discount_text') ?: null;
        $ctaText = $request->input('cta_text') ?: null;

        // Process Desktop / Main Banner Image
        $image = null;
        if ($request->filled('image_base64') && str_starts_with($request->input('image_base64'), 'data:image')) {
            $image = $this->uploadBase64Image($request->input('image_base64'), 'banners', 1920, 800);
        } elseif ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $this->uploadImage($request->file('image'), 'banners', 1920, 800);
        } elseif ($request->filled('image_url')) {
            $image = trim($request->input('image_url'));
        } elseif ($request->filled('image_fallback')) {
            $image = trim($request->input('image_fallback'));
        } else {
            $image = '/hero_honey.png';
        }

        // Process Mobile Banner Image
        $mobileImage = null;
        if ($request->filled('mobile_image_base64') && str_starts_with($request->input('mobile_image_base64'), 'data:image')) {
            $mobileImage = $this->uploadBase64Image($request->input('mobile_image_base64'), 'banners', 800, 800);
        } elseif ($request->hasFile('mobile_image') && $request->file('mobile_image')->isValid()) {
            $mobileImage = $this->uploadImage($request->file('mobile_image'), 'banners', 800, 800);
        } elseif ($request->filled('mobile_image_url')) {
            $mobileImage = trim($request->input('mobile_image_url'));
        }

        // Process Background Pattern / Left Image (Optional)
        $leftImage = null;
        if ($request->hasFile('left_image') && $request->file('left_image')->isValid()) {
            $leftImage = $this->uploadImage($request->file('left_image'), 'banners', 800, 800);
        } elseif ($request->filled('left_image_base64') && str_starts_with($request->input('left_image_base64'), 'data:image')) {
            $leftImage = $this->uploadBase64Image($request->input('left_image_base64'), 'banners', 800, 800);
        } elseif ($request->filled('left_image_url')) {
            $leftImage = trim($request->input('left_image_url'));
        }

        $isActive = true;
        if ($request->has('is_active')) {
            $rawStatus = $request->input('is_active');
            $isActive = !($rawStatus === '0' || $rawStatus === 0 || $rawStatus === false || $rawStatus === 'false');
        }

        $menuLocation = $request->input('menu_location') ?: 'home_hero_slider';

        Banner::create([
            'badge' => $badge,
            'title' => $title ?: null,
            'title_line1' => $titleLine1 ?: null,
            'title_line2' => $titleLine2 ?: null,
            'subtitle' => $subtitle,
            'cta_text' => $ctaText,
            'cta_link' => $request->input('cta_link') ?: '/',
            'image' => $image,
            'mobile_image' => $mobileImage ?: $image,
            'left_image' => $leftImage,
            'bg_color' => $request->input('bg_color') ?: '#002B49',
            'right_bg_color' => $request->input('right_bg_color') ?: ($request->input('bg_color') ?: '#002B49'),
            'order' => (int) ($request->input('order') ?? 1),
            'is_active' => $isActive,
            'menu_location' => $menuLocation,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created and published successfully!');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        $menuLocations = self::MENU_LOCATIONS;
        return view('admin.banners.edit', compact('banner', 'menuLocations'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'badge' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'title_line1' => 'nullable|string|max:255',
            'title_line2' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'discount_text' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:100',
            'cta_link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'bg_color' => 'nullable|string|max:50',
            'right_bg_color' => 'nullable|string|max:50',
            'menu_location' => 'nullable|string|max:100',
            'is_active' => 'nullable',
            'image' => 'nullable',
            'image_base64' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
            'remove_image' => 'nullable|boolean',
            'mobile_image' => 'nullable',
            'mobile_image_base64' => 'nullable|string',
            'mobile_image_url' => 'nullable|string|max:500',
            'remove_mobile_image' => 'nullable|boolean',
            'left_image' => 'nullable',
            'left_image_base64' => 'nullable|string',
            'left_image_url' => 'nullable|string|max:500',
            'remove_left_image' => 'nullable|boolean',
        ]);

        $titleLine1 = trim((string) ($request->input('title_line1') ?? ''));
        $titleLine2 = trim((string) ($request->input('title_line2') ?? ''));
        $title = trim((string) ($request->input('title') ?? ''));

        if (empty($title)) {
            $title = trim($titleLine1 . ' ' . $titleLine2);
        }

        $badge = $request->input('badge') ?: $request->input('tagline') ?: null;
        $subtitle = $request->input('subtitle') ?: $request->input('discount_text') ?: null;
        $ctaText = $request->input('cta_text') ?: null;

        // Process Desktop Image
        $image = $banner->image;
        if ($request->filled('image_base64') && str_starts_with($request->input('image_base64'), 'data:image')) {
            $image = $this->uploadBase64Image($request->input('image_base64'), 'banners', 1920, 800);
        } elseif ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $this->uploadImage($request->file('image'), 'banners', 1920, 800);
        } elseif ($request->filled('image_url')) {
            $image = trim($request->input('image_url'));
        } elseif ($request->boolean('remove_image')) {
            $image = '/hero_honey.png';
        }

        // Process Mobile Image
        $mobileImage = $banner->mobile_image;
        if ($request->filled('mobile_image_base64') && str_starts_with($request->input('mobile_image_base64'), 'data:image')) {
            $mobileImage = $this->uploadBase64Image($request->input('mobile_image_base64'), 'banners', 800, 800);
        } elseif ($request->hasFile('mobile_image') && $request->file('mobile_image')->isValid()) {
            $mobileImage = $this->uploadImage($request->file('mobile_image'), 'banners', 800, 800);
        } elseif ($request->filled('mobile_image_url')) {
            $mobileImage = trim($request->input('mobile_image_url'));
        } elseif ($request->boolean('remove_mobile_image')) {
            $mobileImage = null;
        }

        // Process Left Image
        $leftImage = $banner->left_image;
        if ($request->hasFile('left_image') && $request->file('left_image')->isValid()) {
            $leftImage = $this->uploadImage($request->file('left_image'), 'banners', 800, 800);
        } elseif ($request->filled('left_image_base64') && str_starts_with($request->input('left_image_base64'), 'data:image')) {
            $leftImage = $this->uploadBase64Image($request->input('left_image_base64'), 'banners', 800, 800);
        } elseif ($request->filled('left_image_url')) {
            $leftImage = trim($request->input('left_image_url'));
        } elseif ($request->boolean('remove_left_image')) {
            $leftImage = null;
        }

        $isActive = $banner->is_active;
        if ($request->has('is_active')) {
            $rawStatus = $request->input('is_active');
            $isActive = !($rawStatus === '0' || $rawStatus === 0 || $rawStatus === false || $rawStatus === 'false');
        }

        $menuLocation = $request->input('menu_location') ?: ($banner->menu_location ?: 'home_hero_slider');

        $banner->update([
            'badge' => $badge,
            'title' => $title ?: null,
            'title_line1' => $titleLine1 ?: null,
            'title_line2' => $titleLine2 ?: null,
            'subtitle' => $subtitle,
            'cta_text' => $ctaText,
            'cta_link' => $request->input('cta_link') ?: '/',
            'image' => $image,
            'mobile_image' => $mobileImage ?: $image,
            'left_image' => $leftImage,
            'bg_color' => $request->input('bg_color') ?: '#002B49',
            'right_bg_color' => $request->input('right_bg_color') ?: ($request->input('bg_color') ?: '#002B49'),
            'order' => (int) ($request->input('order') ?? $banner->order ?? 1),
            'is_active' => $isActive,
            'menu_location' => $menuLocation,
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return back()->with('success', 'Banner status changed to ' . ($banner->is_active ? 'Active' : 'Inactive') . '!');
    }

    public function reorder(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $direction = $request->input('direction');

        if ($direction === 'up') {
            $prev = Banner::where('order', '<', $banner->order)->orderBy('order', 'desc')->first();
            if ($prev) {
                $tempOrder = $banner->order;
                $banner->order = $prev->order;
                $prev->order = $tempOrder;
                $banner->save();
                $prev->save();
            }
        } elseif ($direction === 'down') {
            $next = Banner::where('order', '>', $banner->order)->orderBy('order', 'asc')->first();
            if ($next) {
                $tempOrder = $banner->order;
                $banner->order = $next->order;
                $next->order = $tempOrder;
                $banner->save();
                $next->save();
            }
        }

        return back()->with('success', 'Banner display order updated!');
    }
}
