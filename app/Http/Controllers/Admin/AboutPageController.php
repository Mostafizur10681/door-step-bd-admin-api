<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutPageController extends Controller
{
    use UploadImageTrait;

    public function index()
    {
        $about = AboutPage::first() ?: new AboutPage();
        return view('admin.about.index', compact('about'));
    }

    public function update(Request $request)
    {
        $about = AboutPage::first() ?: new AboutPage();

        $validated = $request->validate([
            // Hero Section
            'hero_badge' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',

            // Story Section
            'story_badge' => 'nullable|string|max:255',
            'story_title' => 'nullable|string|max:255',
            'story_description_1' => 'nullable|string',
            'story_description_2' => 'nullable|string',
            'story_since' => 'nullable|string|max:255',
            'experience_badge_text' => 'nullable|string|max:255',
            'experience_badge_subtext' => 'nullable|string|max:255',
            'story_points' => 'nullable|array',
            'story_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',
            'story_image_base64' => 'nullable|string',
            'remove_story_image' => 'nullable|boolean',

            // Mission & Vision
            'mission_title' => 'nullable|string|max:255',
            'mission_description' => 'nullable|string',
            'vision_title' => 'nullable|string|max:255',
            'vision_description' => 'nullable|string',

            // Why Choose Us / Core Values
            'why_choose_badge' => 'nullable|string|max:255',
            'why_choose_title' => 'nullable|string|max:255',
            'why_choose_subtitle' => 'nullable|string',
            'features' => 'nullable|array',

            // Stats
            'stats' => 'nullable|array',

            // Team
            'team_badge' => 'nullable|string|max:255',
            'team_title' => 'nullable|string|max:255',
            'team_subtitle' => 'nullable|string',
            'team' => 'nullable|array',
            'team_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif|max:10240',

            // CTA / Contact Section
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
            'cta_phone' => 'nullable|string|max:255',
            'cta_email' => 'nullable|string|max:255',
        ]);

        $data = $validated;

        // Process Story Image -> Store as Base64 in Database
        if ($request->hasFile('story_image') && $request->file('story_image')->isValid()) {
            $file = $request->file('story_image');
            $mimeType = $file->getMimeType() ?: 'image/jpeg';
            $base64 = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            $data['story_image'] = $base64;
        } elseif ($request->filled('story_image_base64')) {
            $data['story_image'] = $request->input('story_image_base64');
        } elseif ($request->boolean('remove_story_image')) {
            $data['story_image'] = null;
        } else {
            // Keep existing story image
            $data['story_image'] = $about->story_image;
        }

        // Clean & normalize Story Points array
        if (isset($data['story_points']) && is_array($data['story_points'])) {
            $data['story_points'] = array_values(array_filter($data['story_points'], function ($item) {
                return filled(trim($item ?? ''));
            }));
        } else {
            $data['story_points'] = [];
        }

        // Clean & normalize Stats array
        if (isset($data['stats']) && is_array($data['stats'])) {
            $data['stats'] = array_values(array_filter($data['stats'], function ($item) {
                return filled($item['value'] ?? null) || filled($item['label'] ?? null);
            }));
        } else {
            $data['stats'] = [];
        }

        // Clean & normalize Features array
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_values(array_filter($data['features'], function ($item) {
                return filled($item['title'] ?? null);
            }));
        } else {
            $data['features'] = [];
        }

        // Clean & normalize Team array & convert team images to Base64
        $team = $request->input('team', []);
        $teamImages = $request->file('team_images', []);
        $processedTeam = [];

        if (is_array($team)) {
            foreach ($team as $index => $member) {
                if (empty($member['name']) && empty($member['role'])) {
                    continue;
                }

                $memberImage = $member['image'] ?? null;

                // Check if a file was uploaded for this member index
                if (isset($teamImages[$index]) && $teamImages[$index]->isValid()) {
                    $file = $teamImages[$index];
                    $mimeType = $file->getMimeType() ?: 'image/jpeg';
                    $memberImage = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
                }

                $processedTeam[] = [
                    'name' => $member['name'] ?? '',
                    'role' => $member['role'] ?? '',
                    'bio' => $member['bio'] ?? '',
                    'image' => $memberImage,
                ];
            }
        }
        $data['team'] = $processedTeam;

        // Save into MySQL
        $about->fill($data);
        $about->save();

        return back()->with('success', 'About page content and Base64 images updated successfully!');
    }
}
