<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\Request;

class FooterSettingController extends Controller
{
    public function index()
    {
        $footer = FooterSetting::getOrCreate();
        return view('admin.footer-settings.index', compact('footer'));
    }

    public function update(Request $request)
    {
        $footer = FooterSetting::getOrCreate();

        $validated = $request->validate([
            'store_name'        => 'nullable|string|max:100',
            'logo_image'        => 'nullable|image|mimes:jpeg,png,jpg,webp,svg,gif|max:5120',
            'logo_image_base64' => 'nullable|string',
            'remove_logo_image' => 'nullable|boolean',
            'address'           => 'nullable|string',
            'map_url'           => 'nullable|string|max:500',
            'contact_phone'     => 'nullable|string|max:50',
            'contact_email'     => 'nullable|string|max:100',
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
            'copyright_text'    => 'nullable|string|max:255',
            'payment_methods'   => 'nullable|array',
        ]);

        $data = $validated;

        // Process Logo Image -> Store as Base64 in Database
        if ($request->hasFile('logo_image') && $request->file('logo_image')->isValid()) {
            $file = $request->file('logo_image');
            $mimeType = $file->getMimeType() ?: 'image/png';
            $base64 = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            $data['logo_image'] = $base64;
        } elseif ($request->filled('logo_image_base64')) {
            $data['logo_image'] = $request->input('logo_image_base64');
        } elseif ($request->boolean('remove_logo_image')) {
            $data['logo_image'] = null;
        } else {
            $data['logo_image'] = $footer->logo_image;
        }

        // Clean & normalize column 1 links
        if (isset($data['column_1_links']) && is_array($data['column_1_links'])) {
            $data['column_1_links'] = array_values(array_filter($data['column_1_links'], function ($item) {
                return !empty(trim($item['label'] ?? ''));
            }));
        } else {
            $data['column_1_links'] = [];
        }

        // Clean & normalize column 2 links
        if (isset($data['column_2_links']) && is_array($data['column_2_links'])) {
            $data['column_2_links'] = array_values(array_filter($data['column_2_links'], function ($item) {
                return !empty(trim($item['label'] ?? ''));
            }));
        } else {
            $data['column_2_links'] = [];
        }

        // Clean & normalize column 3 links
        if (isset($data['column_3_links']) && is_array($data['column_3_links'])) {
            $data['column_3_links'] = array_values(array_filter($data['column_3_links'], function ($item) {
                return !empty(trim($item['label'] ?? ''));
            }));
        } else {
            $data['column_3_links'] = [];
        }

        // Clean payment methods
        if (isset($data['payment_methods']) && is_array($data['payment_methods'])) {
            $data['payment_methods'] = array_values(array_filter(array_map('trim', $data['payment_methods'])));
        } else {
            $data['payment_methods'] = [];
        }

        // Sync legacy/alias fields
        $data['contact_address'] = $data['address'] ?? $footer->contact_address;
        $data['contact_hours'] = ($data['working_hours_1'] ?? '') . ($data['working_hours_2'] ? ', ' . $data['working_hours_2'] : '');

        $footer->fill($data);
        $footer->save();

        return back()->with('success', 'Footer settings updated successfully!');
    }
}
