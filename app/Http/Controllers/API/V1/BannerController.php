<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Http\Resources\API\V1\BannerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Banner::query();

        if ($request->boolean('public')) {
            $query->where('is_active', true);
        }

        if ($request->filled('menu')) {
            $query->where('menu_location', $request->input('menu'));
        }

        $banners = $query->orderBy('order', 'asc')->orderBy('id', 'asc')->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Banners retrieved successfully',
            'data' => BannerResource::collection($banners)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'title_line1' => 'nullable|string|max:255',
            'title_line2' => 'nullable|string|max:255',
            'titleLine1' => 'nullable|string|max:255',
            'titleLine2' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'discount_text' => 'nullable|string|max:255',
            'discountText' => 'nullable|string|max:255',
            'image' => 'nullable',
            'desktop_image' => 'nullable',
            'desktopImage' => 'nullable',
            'mobile_image' => 'nullable',
            'mobileImage' => 'nullable',
            'left_image' => 'nullable',
            'bg_color' => 'nullable|string|max:50',
            'bgColor' => 'nullable|string|max:50',
            'right_bg_color' => 'nullable|string|max:50',
            'badge' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:255',
            'ctaText' => 'nullable|string|max:255',
            'cta_link' => 'nullable|string|max:255',
            'ctaLink' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'menu_location' => 'nullable|string|max:255',
        ]);

        $titleLine1 = $validated['title_line1'] ?? $validated['titleLine1'] ?? null;
        $titleLine2 = $validated['title_line2'] ?? $validated['titleLine2'] ?? null;
        $title = $validated['title'] ?? trim(($titleLine1 ?? '') . ' ' . ($titleLine2 ?? '')) ?: null;
        $subtitle = $validated['subtitle'] ?? $validated['discount_text'] ?? $validated['discountText'] ?? null;
        $badge = $validated['badge'] ?? $validated['tagline'] ?? null;
        $ctaText = $validated['cta_text'] ?? $validated['ctaText'] ?? null;
        $ctaLink = $validated['cta_link'] ?? $validated['ctaLink'] ?? $validated['link'] ?? '/';
        $bgColor = $validated['bg_color'] ?? $validated['bgColor'] ?? '#002B49';

        // Process Main / Desktop Image
        $image = '/hero_honey.png';
        $reqImage = $request->file('image') ?: $request->file('desktop_image') ?: $request->file('desktopImage');
        if ($reqImage && $reqImage->isValid()) {
            $mimeType = $reqImage->getMimeType() ?: 'image/jpeg';
            $image = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($reqImage->getRealPath()));
        } else {
            $rawImg = $request->input('image') ?: $request->input('desktop_image') ?: $request->input('desktopImage');
            if (is_string($rawImg) && filled($rawImg)) {
                $image = $rawImg;
            }
        }

        // Process Mobile Image
        $mobileImage = null;
        $reqMobile = $request->file('mobile_image') ?: $request->file('mobileImage');
        if ($reqMobile && $reqMobile->isValid()) {
            $mimeType = $reqMobile->getMimeType() ?: 'image/jpeg';
            $mobileImage = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($reqMobile->getRealPath()));
        } else {
            $rawMobile = $request->input('mobile_image') ?: $request->input('mobileImage');
            if (is_string($rawMobile) && filled($rawMobile)) {
                $mobileImage = $rawMobile;
            }
        }

        // Process Left Image
        $leftImage = null;
        if ($request->hasFile('left_image') && $request->file('left_image')->isValid()) {
            $file = $request->file('left_image');
            $mimeType = $file->getMimeType() ?: 'image/jpeg';
            $leftImage = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        } else if (is_string($request->input('left_image'))) {
            $leftImage = $request->input('left_image');
        }

        $banner = Banner::create([
            'title' => $title,
            'title_line1' => $titleLine1,
            'title_line2' => $titleLine2,
            'subtitle' => $subtitle,
            'image' => $image,
            'mobile_image' => $mobileImage ?: $image,
            'left_image' => $leftImage,
            'bg_color' => $bgColor,
            'right_bg_color' => $validated['right_bg_color'] ?? $bgColor,
            'badge' => $badge,
            'cta_text' => $ctaText,
            'cta_link' => $ctaLink,
            'order' => (int) ($validated['order'] ?? 1),
            'is_active' => $validated['is_active'] ?? true,
            'menu_location' => $validated['menu_location'] ?? 'home_hero_slider',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Banner created successfully',
            'data' => new BannerResource($banner)
        ], 201);
    }

    public function show(Banner $banner): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Banner retrieved successfully',
            'data' => new BannerResource($banner)
        ]);
    }

    public function update(Request $request, Banner $banner): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'title_line1' => 'nullable|string|max:255',
            'title_line2' => 'nullable|string|max:255',
            'titleLine1' => 'nullable|string|max:255',
            'titleLine2' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'discount_text' => 'nullable|string|max:255',
            'discountText' => 'nullable|string|max:255',
            'image' => 'nullable',
            'desktop_image' => 'nullable',
            'desktopImage' => 'nullable',
            'mobile_image' => 'nullable',
            'mobileImage' => 'nullable',
            'left_image' => 'nullable',
            'bg_color' => 'nullable|string|max:50',
            'bgColor' => 'nullable|string|max:50',
            'right_bg_color' => 'nullable|string|max:50',
            'badge' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:255',
            'ctaText' => 'nullable|string|max:255',
            'cta_link' => 'nullable|string|max:255',
            'ctaLink' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'menu_location' => 'nullable|string|max:255',
        ]);

        $updateData = [];

        if (array_key_exists('title_line1', $validated) || array_key_exists('titleLine1', $validated)) {
            $updateData['title_line1'] = $validated['title_line1'] ?? $validated['titleLine1'] ?? null;
        }
        if (array_key_exists('title_line2', $validated) || array_key_exists('titleLine2', $validated)) {
            $updateData['title_line2'] = $validated['title_line2'] ?? $validated['titleLine2'] ?? null;
        }
        if (array_key_exists('title', $validated)) {
            $updateData['title'] = $validated['title'];
        } elseif (isset($updateData['title_line1']) || isset($updateData['title_line2'])) {
            $updateData['title'] = trim(($updateData['title_line1'] ?? $banner->title_line1 ?? '') . ' ' . ($updateData['title_line2'] ?? $banner->title_line2 ?? '')) ?: $banner->title;
        }

        if (array_key_exists('subtitle', $validated) || array_key_exists('discount_text', $validated) || array_key_exists('discountText', $validated)) {
            $updateData['subtitle'] = $validated['subtitle'] ?? $validated['discount_text'] ?? $validated['discountText'] ?? null;
        }
        if (array_key_exists('badge', $validated) || array_key_exists('tagline', $validated)) {
            $updateData['badge'] = $validated['badge'] ?? $validated['tagline'] ?? null;
        }
        if (array_key_exists('cta_text', $validated) || array_key_exists('ctaText', $validated)) {
            $updateData['cta_text'] = $validated['cta_text'] ?? $validated['ctaText'] ?? null;
        }
        if (array_key_exists('cta_link', $validated) || array_key_exists('ctaLink', $validated) || array_key_exists('link', $validated)) {
            $updateData['cta_link'] = $validated['cta_link'] ?? $validated['ctaLink'] ?? $validated['link'] ?? '/';
        }
        if (array_key_exists('bg_color', $validated) || array_key_exists('bgColor', $validated)) {
            $updateData['bg_color'] = $validated['bg_color'] ?? $validated['bgColor'] ?? '#002B49';
        }
        if (array_key_exists('right_bg_color', $validated)) {
            $updateData['right_bg_color'] = $validated['right_bg_color'];
        }
        if (array_key_exists('order', $validated)) {
            $updateData['order'] = (int) $validated['order'];
        }
        if (array_key_exists('is_active', $validated)) {
            $updateData['is_active'] = (bool) $validated['is_active'];
        }
        if (array_key_exists('menu_location', $validated)) {
            $updateData['menu_location'] = $validated['menu_location'];
        }

        // Process Main / Desktop Image
        $reqImage = $request->file('image') ?: $request->file('desktop_image') ?: $request->file('desktopImage');
        if ($reqImage && $reqImage->isValid()) {
            $mimeType = $reqImage->getMimeType() ?: 'image/jpeg';
            $updateData['image'] = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($reqImage->getRealPath()));
        } else {
            $rawImg = $request->input('image') ?: $request->input('desktop_image') ?: $request->input('desktopImage');
            if (is_string($rawImg) && filled($rawImg)) {
                $updateData['image'] = $rawImg;
            }
        }

        // Process Mobile Image
        $reqMobile = $request->file('mobile_image') ?: $request->file('mobileImage');
        if ($reqMobile && $reqMobile->isValid()) {
            $mimeType = $reqMobile->getMimeType() ?: 'image/jpeg';
            $updateData['mobile_image'] = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($reqMobile->getRealPath()));
        } else {
            $rawMobile = $request->input('mobile_image') ?: $request->input('mobileImage');
            if (is_string($rawMobile) && filled($rawMobile)) {
                $updateData['mobile_image'] = $rawMobile;
            }
        }

        // Process Left Image
        if ($request->hasFile('left_image') && $request->file('left_image')->isValid()) {
            $file = $request->file('left_image');
            $mimeType = $file->getMimeType() ?: 'image/jpeg';
            $updateData['left_image'] = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        } else if (is_string($request->input('left_image')) && filled($request->input('left_image'))) {
            $updateData['left_image'] = $request->input('left_image');
        }

        $banner->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully',
            'data' => new BannerResource($banner->fresh())
        ]);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        if ($banner->image && str_contains($banner->image, 'storage/banners/')) {
            $oldPath = str_replace(url('storage') . '/', '', $banner->image);
            Storage::disk('public')->delete($oldPath);
        }

        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully'
        ]);
    }
}
