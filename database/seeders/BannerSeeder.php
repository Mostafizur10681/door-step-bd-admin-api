<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'id' => 1,
                'badge' => '100% PURE & NATURAL HARVEST',
                'title_line1' => 'PURE ORGANIC',
                'title_line2' => 'MEGA SALE',
                'title' => 'PURE ORGANIC MEGA SALE',
                'subtitle' => 'UP TO 40% OFF ON RAW SUNDARBAN HONEY',
                'cta_text' => 'SHOP NOW',
                'cta_link' => '/all-products?category=organic-food',
                'image' => '/hero_honey.png',
                'mobile_image' => '/hero_honey.png',
                'left_image' => null,
                'bg_color' => '#002B49',
                'right_bg_color' => '#002B49',
                'order' => 1,
                'is_active' => true,
                'menu_location' => 'home_hero_slider',
            ],
            [
                'id' => 2,
                'badge' => 'DERMATOLOGICALLY TESTED',
                'title_line1' => 'GLOW NATURALLY',
                'title_line2' => 'SKINCARE ESSENTIALS',
                'title' => 'GLOW NATURALLY SKINCARE ESSENTIALS',
                'subtitle' => 'FLAT 25% OFF ON PREMIUM BEAUTY RANGE',
                'cta_text' => 'SHOP NOW',
                'cta_link' => '/all-products?category=beauty',
                'image' => '/hero_beauty.png',
                'mobile_image' => '/hero_beauty.png',
                'left_image' => null,
                'bg_color' => '#2d1b2e',
                'right_bg_color' => '#2d1b2e',
                'order' => 2,
                'is_active' => true,
                'menu_location' => 'home_hero_slider',
            ],
            [
                'id' => 3,
                'badge' => 'AUTHENTIC GLOBAL BRANDS',
                'title_line1' => 'BOOST YOUR IMMUNITY',
                'title_line2' => 'HEALTH SUPPLEMENTS',
                'title' => 'BOOST YOUR IMMUNITY HEALTH SUPPLEMENTS',
                'subtitle' => 'BUY 1 GET 1 30% OFF TODAY',
                'cta_text' => 'SHOP NOW',
                'cta_link' => '/all-products?category=food-supplements',
                'image' => '/hero_chia.png',
                'mobile_image' => '/hero_chia.png',
                'left_image' => null,
                'bg_color' => '#0b2b26',
                'right_bg_color' => '#0b2b26',
                'order' => 3,
                'is_active' => true,
                'menu_location' => 'home_hero_slider',
            ],
            [
                'id' => 4,
                'badge' => 'RICH IN OMEGA-3 & FIBER',
                'title_line1' => 'SUPERFOOD NUTRITION',
                'title_line2' => 'CHIA & BLACKSEED',
                'title' => 'SUPERFOOD NUTRITION CHIA & BLACKSEED',
                'subtitle' => 'SPECIAL COMBO BUNDLE AT BEST PRICE',
                'cta_text' => 'SHOP NOW',
                'cta_link' => '/all-products?category=organic-food',
                'image' => '/hero_chia.png',
                'mobile_image' => '/hero_chia.png',
                'left_image' => null,
                'bg_color' => '#1e130c',
                'right_bg_color' => '#1e130c',
                'order' => 4,
                'is_active' => true,
                'menu_location' => 'home_hero_slider',
            ],
            [
                'id' => 5,
                'badge' => 'TOXIN FREE & EXTRA SOFT',
                'title_line1' => 'GENTLE & SAFE',
                'title_line2' => 'BABY ESSENTIALS',
                'title' => 'GENTLE & SAFE BABY ESSENTIALS',
                'subtitle' => 'SAVE UP TO 35% ON BABY CARE PACKS',
                'cta_text' => 'SHOP NOW',
                'cta_link' => '/all-products?category=babies-hub',
                'image' => '/hero_baby.png',
                'mobile_image' => '/hero_baby.png',
                'left_image' => null,
                'bg_color' => '#0f2b3e',
                'right_bg_color' => '#0f2b3e',
                'order' => 5,
                'is_active' => true,
                'menu_location' => 'home_hero_slider',
            ],
        ];

        foreach ($sliders as $slider) {
            Banner::updateOrCreate(
                ['id' => $slider['id']],
                $slider
            );
        }
    }
}
