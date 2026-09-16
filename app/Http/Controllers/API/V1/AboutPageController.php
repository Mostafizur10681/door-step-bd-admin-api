<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Http\Resources\API\V1\AboutPageResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutPageController extends Controller
{
    public function index(): JsonResponse
    {
        $about = AboutPage::first();
        
        if (!$about) {
            $about = new AboutPage();
        }

        return response()->json([
            'success' => true,
            'message' => 'About page content retrieved successfully',
            'data' => new AboutPageResource($about)
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $about = AboutPage::first() ?: new AboutPage();

        $data = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_badge' => 'nullable|string|max:255',
            'story_title' => 'nullable|string|max:255',
            'story_badge' => 'nullable|string|max:255',
            'story_description_1' => 'nullable|string',
            'story_description_2' => 'nullable|string',
            'story_since' => 'nullable|string|max:255',
            'experience_badge_text' => 'nullable|string|max:255',
            'experience_badge_subtext' => 'nullable|string|max:255',
            'story_points' => 'nullable|array',
            'story_image' => 'nullable|string',
            'remove_story_image' => 'nullable|boolean',
            'mission_title' => 'nullable|string|max:255',
            'mission_description' => 'nullable|string',
            'vision_title' => 'nullable|string|max:255',
            'vision_description' => 'nullable|string',
            'why_choose_badge' => 'nullable|string|max:255',
            'why_choose_title' => 'nullable|string|max:255',
            'why_choose_subtitle' => 'nullable|string',
            'features' => 'nullable|array',
            'stats' => 'nullable|array',
            'team_badge' => 'nullable|string|max:255',
            'team_title' => 'nullable|string|max:255',
            'team_subtitle' => 'nullable|string',
            'team' => 'nullable|array',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
            'cta_phone' => 'nullable|string|max:255',
            'cta_email' => 'nullable|string|max:255',
        ]);

        if ($request->boolean('remove_story_image')) {
            $data['story_image'] = null;
        }

        // Clean & normalize Story Points
        if (isset($data['story_points']) && is_array($data['story_points'])) {
            $data['story_points'] = array_values(array_filter($data['story_points'], function ($item) {
                return filled(trim($item ?? ''));
            }));
        }

        // Clean & normalize Stats
        if (isset($data['stats']) && is_array($data['stats'])) {
            $data['stats'] = array_values(array_filter($data['stats'], function ($item) {
                return filled($item['value'] ?? null) || filled($item['label'] ?? null);
            }));
        }

        // Clean & normalize Features
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_values(array_filter($data['features'], function ($item) {
                return filled($item['title'] ?? null);
            }));
        }

        // Clean & normalize Team
        if (isset($data['team']) && is_array($data['team'])) {
            $data['team'] = array_values(array_filter($data['team'], function ($member) {
                return filled($member['name'] ?? null) || filled($member['role'] ?? null);
            }));
        }

        $about->fill($data);
        $about->save();

        return response()->json([
            'success' => true,
            'message' => 'About page content updated successfully',
            'data' => new AboutPageResource($about)
        ]);
    }
}
