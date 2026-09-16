<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FooterSettingController extends Controller
{
    /**
     * Public: Return the footer settings.
     */
    public function index(): JsonResponse
    {
        $settings = FooterSetting::getOrCreate();

        return response()->json([
            'success' => true,
            'message' => 'Footer settings retrieved successfully',
            'data'    => $settings,
        ]);
    }

    /**
     * Admin: Update footer settings via API.
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'store_name'        => 'nullable|string|max:100',
            'store_icon'        => 'nullable|string|max:20',
            'store_description' => 'nullable|string|max:500',
            'logo_image'        => 'nullable|string',
            'address'           => 'nullable|string',
            'map_url'           => 'nullable|string|max:500',
            'copyright_text'    => 'nullable|string|max:255',
            'contact_phone'     => 'nullable|string|max:50',
            'contact_email'     => 'nullable|email|max:100',
            'working_hours_1'   => 'nullable|string|max:200',
            'working_hours_2'   => 'nullable|string|max:200',
            'facebook_url'      => 'nullable|string|max:255',
            'instagram_url'     => 'nullable|string|max:255',
            'youtube_url'       => 'nullable|string|max:255',
            'pinterest_url'     => 'nullable|string|max:255',
            'linkedin_url'      => 'nullable|string|max:255',
            'twitter_url'       => 'nullable|string|max:255',
            'tiktok_url'        => 'nullable|string|max:255',
            'column_1_title'    => 'nullable|string|max:100',
            'column_1_links'    => 'nullable|array',
            'column_2_title'    => 'nullable|string|max:100',
            'column_2_links'    => 'nullable|array',
            'column_3_title'    => 'nullable|string|max:100',
            'column_3_links'    => 'nullable|array',
            'payment_methods'   => 'nullable|array',
        ]);

        $settings = FooterSetting::getOrCreate();
        $settings->fill($data);
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Footer settings updated successfully',
            'data'    => $settings,
        ]);
    }
}
