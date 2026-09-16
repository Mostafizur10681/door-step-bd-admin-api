<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_icon',
        'store_description',
        'logo_image',
        'address',
        'map_url',
        'copyright_text',
        'social_links',
        'quick_links',
        'service_links',
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_hours',
        'working_hours_1',
        'working_hours_2',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'pinterest_url',
        'linkedin_url',
        'twitter_url',
        'tiktok_url',
        'column_1_title',
        'column_1_links',
        'column_2_title',
        'column_2_links',
        'column_3_title',
        'column_3_links',
        'payment_methods',
    ];

    protected $casts = [
        'social_links'    => 'array',
        'quick_links'     => 'array',
        'service_links'   => 'array',
        'column_1_links'  => 'array',
        'column_2_links'  => 'array',
        'column_3_links'  => 'array',
        'payment_methods' => 'array',
    ];

    /**
     * Get or create the single settings row with sensible defaults.
     */
    public static function getOrCreate(): self
    {
        $settings = self::first();

        if (!$settings) {
            $settings = self::create([
                'store_name'        => 'Shopia',
                'store_icon'        => '🛍️',
                'store_description' => 'Your trusted online shopping destination in Bangladesh for authentic organic food, health & beauty products.',
                'logo_image'        => null,
                'address'           => "41/1, Sher-E-Bangla Rd,\nMohammadpur, Dhaka 1207",
                'contact_address'   => "41/1, Sher-E-Bangla Rd, Mohammadpur, Dhaka 1207",
                'map_url'           => 'https://maps.google.com/?q=Mohammadpur+Dhaka',
                'copyright_text'    => 'Copyright © ' . date('Y') . ' Shopia. All Rights Reserved',
                'contact_phone'     => '01681-135030',
                'contact_email'     => 'info@shopiabd.com',
                'working_hours_1'   => 'Saturday- Thursday: 9:00am- 10:00pm',
                'working_hours_2'   => 'Friday: 15:00pm – 11:00pm',
                'contact_hours'     => 'Sat-Thu: 9AM-10PM, Fri: 3PM-11PM',
                'facebook_url'      => 'https://facebook.com/shopiabd',
                'instagram_url'     => 'https://instagram.com/shopiabd',
                'youtube_url'       => 'https://youtube.com/@shopiabd',
                'pinterest_url'     => 'https://pinterest.com/shopiabd',
                'linkedin_url'      => 'https://linkedin.com/company/shopiabd',
                'twitter_url'       => 'https://twitter.com/shopiabd',
                'tiktok_url'        => '',
                'column_1_title'    => 'Information',
                'column_1_links'    => [
                    ['label' => 'About us', 'url' => '/about'],
                    ['label' => 'Blog & Journal', 'url' => '/blog'],
                    ['label' => 'FAQ & Support', 'url' => '/faq'],
                    ['label' => 'Delivery information', 'url' => '/delivery'],
                    ['label' => 'Privacy Policy', 'url' => '/privacy'],
                    ['label' => 'Sales', 'url' => '/sales'],
                    ['label' => 'Terms & Conditions', 'url' => '/terms'],
                ],
                'column_2_title'    => 'Account',
                'column_2_links'    => [
                    ['label' => 'My account', 'url' => '/account'],
                    ['label' => 'My orders', 'url' => '/dashboard?tab=orders'],
                    ['label' => 'Returns', 'url' => '/returns'],
                    ['label' => 'Shipping', 'url' => '/shipping'],
                    ['label' => 'Wishlist', 'url' => '/wishlist'],
                ],
                'column_3_title'    => 'Store',
                'column_3_links'    => [
                    ['label' => 'Bestsellers', 'url' => '/bestsellers'],
                    ['label' => 'Discount', 'url' => '/discount'],
                    ['label' => 'Latest products', 'url' => '/latest'],
                    ['label' => 'Sale', 'url' => '/sale'],
                ],
                'payment_methods'   => ['BKASH', 'ROCKET', 'NAGAD', 'VISA', 'MASTERCARD', 'AMEX'],
            ]);
        }

        return $settings;
    }
}
