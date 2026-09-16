<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;

class WhatsAppSettingController extends Controller
{
    /**
     * Show the WhatsApp Connection Settings page.
     */
    public function index()
    {
        $setting = ContactSetting::first();
        if (!$setting) {
            $setting = ContactSetting::create([
                'phone' => '+880 1800-000000',
                'email' => 'support@shopia.com',
                'address' => '41/1, Sher-E-Bangla Rd, Mohammadpur, Dhaka 1207',
                'business_hours_weekday' => 'Saturday - Thursday: 9:00 AM - 10:00 PM',
                'business_hours_weekend' => 'Friday: 3:00 PM - 10:00 PM',
                'support_title' => 'Need Immediate Assistance?',
                'support_desc' => 'Our customer service specialists are active and eager to assist you right now.',
                'support_phone' => '+880 1800-000000',
                'whatsapp_number' => '8801800000000',
                'whatsapp_is_enabled' => true,
                'whatsapp_default_message' => 'Hello! I have an inquiry regarding your products on Shopia.',
                'whatsapp_position' => 'bottom-right',
                'whatsapp_header_title' => 'Chat with WhatsApp Support',
                'whatsapp_header_subtitle' => 'Typically replies in a few minutes',
                'whatsapp_button_text' => 'Start WhatsApp Chat',
                'whatsapp_show_floating_button' => true,
            ]);
        }

        return view('admin.whatsapp-settings.index', compact('setting'));
    }

    /**
     * Update WhatsApp Connection Settings.
     */
    public function update(Request $request)
    {
        $setting = ContactSetting::first();
        if (!$setting) {
            $setting = new ContactSetting();
        }

        $data = $request->validate([
            'whatsapp_number' => 'required|string|max:100',
            'whatsapp_is_enabled' => 'nullable|boolean',
            'whatsapp_default_message' => 'nullable|string|max:1000',
            'whatsapp_position' => 'required|string|in:bottom-right,bottom-left',
            'whatsapp_header_title' => 'nullable|string|max:255',
            'whatsapp_header_subtitle' => 'nullable|string|max:255',
            'whatsapp_button_text' => 'nullable|string|max:255',
            'whatsapp_show_floating_button' => 'nullable|boolean',
        ]);

        $data['whatsapp_is_enabled'] = $request->boolean('whatsapp_is_enabled');
        $data['whatsapp_show_floating_button'] = $request->boolean('whatsapp_show_floating_button');

        $setting->fill($data);
        $setting->save();

        return back()->with('success', 'WhatsApp Connection settings updated successfully!');
    }
}
