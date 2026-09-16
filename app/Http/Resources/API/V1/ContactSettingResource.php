<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $imageUrl = $this->support_image;
        if ($imageUrl && !str_starts_with($imageUrl, 'http') && !str_starts_with($imageUrl, 'https') && !str_starts_with($imageUrl, 'data:image')) {
            $imageUrl = url('storage/' . $imageUrl);
        }

        return [
            'id' => (string) $this->id,
            'badge_text' => $this->badge_text ?? '💬 Get in Touch • 24/7 Support',
            'hero_title' => $this->hero_title ?? "We're Here to Help You Thrive",
            'hero_subtitle' => $this->hero_subtitle ?? 'Have questions about our organic products, shipping updates, or business partnerships? Our team is always here to assist.',
            'phone' => $this->phone ?? '+880 1800-000000',
            'secondary_phone' => $this->secondary_phone ?? '+880 1700-000000',
            'email' => $this->email ?? 'support@shopia.com',
            'secondary_email' => $this->secondary_email ?? 'wholesale@shopia.com',
            'whatsapp_number' => $this->whatsapp_number ?? '8801800000000',
            'whatsapp_is_enabled' => (bool) ($this->whatsapp_is_enabled ?? true),
            'whatsapp_default_message' => $this->whatsapp_default_message ?? 'Hello! I have an inquiry regarding your products on Shopia.',
            'whatsapp_position' => $this->whatsapp_position ?? 'bottom-right',
            'whatsapp_header_title' => $this->whatsapp_header_title ?? 'Chat with WhatsApp Support',
            'whatsapp_header_subtitle' => $this->whatsapp_header_subtitle ?? 'Typically replies in a few minutes',
            'whatsapp_button_text' => $this->whatsapp_button_text ?? 'Start WhatsApp Chat',
            'whatsapp_show_floating_button' => (bool) ($this->whatsapp_show_floating_button ?? true),
            'default_message' => $this->whatsapp_default_message ?? 'Hello! I have an inquiry regarding your products on Shopia.',
            'enabled' => (bool) ($this->whatsapp_is_enabled ?? true),
            'position' => str_contains($this->whatsapp_position ?? '', 'left') ? 'left' : 'right',
            'address' => $this->address ?? '41/1, Sher-E-Bangla Rd, Mohammadpur, Dhaka 1207',
            'business_hours_weekday' => $this->business_hours_weekday ?? 'Saturday - Thursday: 9:00 AM - 10:00 PM',
            'business_hours_weekend' => $this->business_hours_weekend ?? 'Friday: 3:00 PM - 10:00 PM',
            'response_time_note' => $this->response_time_note ?? 'Average reply time: Under 15 mins during business hours',
            'map_title' => $this->map_title ?? 'Visit Our Store & Experience Center',
            'map_subtitle' => $this->map_subtitle ?? 'Experience our 100% natural, fresh organic food & wellness products in person.',
            'map_url' => $this->map_url ?? 'https://maps.google.com/maps?q=Mohammadpur%2C%20Dhaka&t=&z=14&ie=UTF8&iwloc=&output=embed',
            'location_directions' => $this->location_directions ?? 'Near Mohammadpur Bus Stand, easy parking available.',
            'form_title' => $this->form_title ?? 'Send Us a Message',
            'form_subtitle' => $this->form_subtitle ?? 'Fill out the form below and our customer support team will respond within 24 hours.',
            'form_topics' => $this->form_topics ?? [
                'Order Tracking & Delivery Status',
                'Product Inquiry & Authenticity',
                'Returns, Refunds & Replacements',
                'Wholesale & B2B Bulk Orders',
                'Payment & Billing Issues',
                'Other General Inquiries'
            ],
            'emergency_notice' => $this->emergency_notice ?? '⚡ Fast Order Hotline: For immediate order modifications or urgent delivery help, call our hotline directly.',
            'features' => $this->features ?? [
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
            'support_title' => $this->support_title ?? 'Need Immediate Assistance?',
            'support_desc' => $this->support_desc ?? 'Our customer service specialists are active and eager to assist you right now.',
            'support_phone' => $this->support_phone ?? '+880 1800-000000',
            'support_image' => $imageUrl,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
