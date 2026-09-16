<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContactSettingController extends Controller
{
    public function index()
    {
        $setting = ContactSetting::first();
        if (!$setting) {
            $setting = ContactSetting::create([
                'badge_text' => '💬 Get in Touch • 24/7 Support',
                'hero_title' => "We're Here to Help You Thrive",
                'hero_subtitle' => 'Have questions about our organic products, shipping updates, or business partnerships? Our team is always here to assist.',
                'phone' => '+880 1800-000000',
                'secondary_phone' => '+880 1700-000000',
                'email' => 'support@shopia.com',
                'secondary_email' => 'wholesale@shopia.com',
                'whatsapp_number' => '8801800000000',
                'address' => '41/1, Sher-E-Bangla Rd, Mohammadpur, Dhaka 1207',
                'business_hours_weekday' => 'Saturday - Thursday: 9:00 AM - 10:00 PM',
                'business_hours_weekend' => 'Friday: 3:00 PM - 10:00 PM',
                'response_time_note' => 'Average reply time: Under 15 mins during business hours',
                'map_title' => 'Visit Our Store & Experience Center',
                'map_subtitle' => 'Experience our 100% natural, fresh organic food & wellness products in person.',
                'map_url' => 'https://maps.google.com/maps?q=Mohammadpur%2C%20Dhaka&t=&z=14&ie=UTF8&iwloc=&output=embed',
                'location_directions' => 'Near Mohammadpur Bus Stand, easy parking available.',
                'form_title' => 'Send Us a Message',
                'form_subtitle' => 'Fill out the form below and our customer support team will respond within 24 hours.',
                'form_topics' => [
                    'Order Tracking & Delivery Status',
                    'Product Inquiry & Authenticity',
                    'Returns, Refunds & Replacements',
                    'Wholesale & B2B Bulk Orders',
                    'Payment & Billing Issues',
                    'Other General Inquiries'
                ],
                'emergency_notice' => '⚡ Fast Order Hotline: For immediate order modifications or urgent delivery help, call our hotline directly.',
                'features' => [
                    [
                        'icon' => 'Headphones',
                        'title' => '24/7 Dedicated Care',
                        'desc' => 'Friendly support team ready to assist via live phone, email & chat.'
                    ],
                    [
                        'icon' => 'ShieldCheck',
                        'title' => '100% Genuine Products',
                        'desc' => 'All items tested and directly sourced with authenticity guarantee.'
                    ],
                    [
                        'icon' => 'Truck',
                        'title' => 'Nationwide Delivery',
                        'desc' => 'Fast delivery across 64 districts in Bangladesh with open parcel check.'
                    ],
                    [
                        'icon' => 'RotateCcw',
                        'title' => '7-Day Easy Returns',
                        'desc' => 'Hassle-free replacement or refund for damaged or inaccurate orders.'
                    ]
                ],
                'support_title' => 'Need Immediate Assistance?',
                'support_desc' => 'Our customer service specialists are active and eager to assist you right now.',
                'support_phone' => '+880 1800-000000',
            ]);
        }

        return view('admin.contact-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = ContactSetting::first();
        if (!$setting) {
            $setting = new ContactSetting();
        }

        $data = $request->validate([
            'badge_text' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'phone' => 'nullable|string|max:100',
            'secondary_phone' => 'nullable|string|max:100',
            'email' => 'nullable|string|max:255',
            'secondary_email' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:100',
            'whatsapp_is_enabled' => 'nullable|boolean',
            'whatsapp_default_message' => 'nullable|string',
            'whatsapp_position' => 'nullable|string|in:bottom-right,bottom-left',
            'whatsapp_header_title' => 'nullable|string|max:255',
            'whatsapp_header_subtitle' => 'nullable|string|max:255',
            'whatsapp_button_text' => 'nullable|string|max:255',
            'whatsapp_show_floating_button' => 'nullable|boolean',
            'address' => 'nullable|string|max:500',
            'business_hours_weekday' => 'nullable|string|max:255',
            'business_hours_weekend' => 'nullable|string|max:255',
            'response_time_note' => 'nullable|string|max:255',
            'map_title' => 'nullable|string|max:255',
            'map_subtitle' => 'nullable|string',
            'map_url' => 'nullable|string',
            'location_directions' => 'nullable|string',
            'form_title' => 'nullable|string|max:255',
            'form_subtitle' => 'nullable|string',
            'form_topics' => 'nullable|array',
            'form_topics.*' => 'nullable|string',
            'emergency_notice' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*.icon' => 'nullable|string',
            'features.*.title' => 'nullable|string',
            'features.*.desc' => 'nullable|string',
            'support_title' => 'nullable|string|max:255',
            'support_desc' => 'nullable|string|max:255',
            'support_phone' => 'nullable|string|max:255',
            'support_image_base64' => 'nullable|string',
            'remove_support_image' => 'nullable|boolean',
        ]);

        if ($request->has('whatsapp_is_enabled')) {
            $data['whatsapp_is_enabled'] = $request->boolean('whatsapp_is_enabled');
        }
        if ($request->has('whatsapp_show_floating_button')) {
            $data['whatsapp_show_floating_button'] = $request->boolean('whatsapp_show_floating_button');
        }

        // Clean arrays
        if (isset($data['form_topics'])) {
            $data['form_topics'] = array_values(array_filter($data['form_topics'], fn($t) => filled($t)));
        }

        if (isset($data['features'])) {
            $data['features'] = array_values(array_filter($data['features'], fn($f) => !empty($f['title'])));
        }

        // Handle Image (Base64 direct database storage)
        if ($request->boolean('remove_support_image')) {
            if ($setting->support_image && str_contains($setting->support_image, 'storage/contact/')) {
                $oldPath = str_replace(url('storage') . '/', '', $setting->support_image);
                Storage::disk('public')->delete($oldPath);
            }
            $data['support_image'] = null;
        } elseif (!empty($data['support_image_base64']) && str_starts_with($data['support_image_base64'], 'data:image')) {
            if ($setting->support_image && str_contains($setting->support_image, 'storage/contact/')) {
                $oldPath = str_replace(url('storage') . '/', '', $setting->support_image);
                Storage::disk('public')->delete($oldPath);
            }
            $data['support_image'] = $data['support_image_base64'];
        }
        unset($data['support_image_base64'], $data['remove_support_image']);

        $setting->fill($data);
        $setting->save();

        return back()->with('success', 'Contact page settings saved successfully!');
    }

    private function handleBase64Image(?string $base64String, string $folder): ?string
    {
        if (!$base64String) {
            return null;
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $data = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                return null;
            }

            $data = base64_decode($data);
            if ($data === false) {
                return null;
            }

            $fileName = Str::random(20) . '.' . $type;
            Storage::disk('public')->put($folder . '/' . $fileName, $data);

            return url('storage/' . $folder . '/' . $fileName);
        }

        return null;
    }
}
