<?php

namespace Database\Seeders;

use App\Models\AboutPage;
use Illuminate\Database\Seeder;

class AboutPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $publicAboutPath = storage_path('app/public/about');
        if (!file_exists($publicAboutPath)) {
            mkdir($publicAboutPath, 0755, true);
        }

        // Copy from frontend src/assets if possible
        $frontendAssetsPath = 'E:/e-commerce/frontend/src/assets';
        $filesToCopy = [
            'about-story.png' => 'about-story.png',
            'team-ceo.png' => 'team-ceo.png',
            'team-founder.png' => 'team-founder.png',
            'team-support.png' => 'team-support.png',
        ];

        foreach ($filesToCopy as $src => $dest) {
            $srcPath = $frontendAssetsPath . '/' . $src;
            $destPath = $publicAboutPath . '/' . $dest;
            if (file_exists($srcPath)) {
                copy($srcPath, $destPath);
            }
        }

        $storyImage = file_exists($publicAboutPath . '/about-story.png')
            ? url('storage/about/about-story.png')
            : 'https://placehold.co/800x600?text=Our+Story';

        $team = [
            [
                'name' => 'Rafiul Islam',
                'role' => 'CEO & Founder',
                'bio' => 'Visionary leader with 10+ years in eCommerce and supply chain management.',
                'image' => file_exists($publicAboutPath . '/team-ceo.png')
                    ? url('storage/about/team-ceo.png')
                    : 'https://placehold.co/150?text=Rafiul+Islam',
            ],
            [
                'name' => 'Nazmun Nahar',
                'role' => 'Co-Founder & COO',
                'bio' => 'Operations expert passionate about logistics, sustainability and customer experience.',
                'image' => file_exists($publicAboutPath . '/team-founder.png')
                    ? url('storage/about/team-founder.png')
                    : 'https://placehold.co/150?text=Nazmun+Nahar',
            ],
            [
                'name' => 'Tanvir Ahmed',
                'role' => 'Head of Support',
                'bio' => 'Dedicated to making every customer interaction seamless and satisfying.',
                'image' => file_exists($publicAboutPath . '/team-support.png')
                    ? url('storage/about/team-support.png')
                    : 'https://placehold.co/150?text=Tanvir+Ahmed',
            ],
        ];

        AboutPage::updateOrCreate(
            ['id' => 1],
            [
                'hero_title' => 'Your Trusted Partner for Pure, Organic & Authentic Living',
                'hero_subtitle' => 'Empowering healthy lifestyles across Bangladesh by bringing 100% natural organic food, premium skincare, and healthcare supplements directly to your home.',
                'hero_badge' => 'Welcome to ShopiaBD',
                'story_title' => 'Bringing Pure & Natural Wellness to Every Home',
                'story_badge' => 'OUR STORY',
                'story_description_1' => 'Founded with a clear vision, ShopiaBD set out to solve the challenge of finding genuine, unadulterated organic products in Bangladesh. We believe that good health starts with authentic food, uncompromised skincare, and pure supplements.',
                'story_description_2' => 'From pure Sundarban wild honey, raw organic chia seeds, and premium maca superfood to dermatologist-approved skincare formulations, every item in our store is selected with utmost care for purity and safety.',
                'story_since' => '2023',
                'experience_badge_text' => '#1',
                'experience_badge_subtext' => 'Authentic E-Commerce In Bangladesh',
                'story_points' => [
                    'Directly Sourced Organic Food',
                    '100% Unadulterated Honey',
                    'Chemical-Free Skincare',
                    'Fast Nationwide COD'
                ],
                'story_image' => $storyImage,
                'mission_title' => 'Our Mission',
                'mission_description' => 'To deliver high-quality authentic and organic products at affordable prices with exceptional customer service, making healthy and reliable living accessible for every household in Bangladesh.',
                'vision_title' => 'Our Vision',
                'vision_description' => 'To become the most trusted and customer-centric lifestyle and wellness e-commerce brand in Bangladesh through unwavering quality, innovation, and direct sourcing.',
                'why_choose_badge' => 'WHY CHOOSE US',
                'why_choose_title' => 'Our Core Promises to You',
                'why_choose_subtitle' => 'Built on transparency, authenticity, and dedication to your health',
                'features' => [
                    [
                        'icon' => 'ShieldCheck',
                        'title' => '100% Authentic & Pure',
                        'desc' => 'Every item in our collection is carefully sourced directly from certified organic farms and global trusted suppliers.',
                        'bgClass' => 'bg-blue-50 dark:bg-blue-900/30'
                    ],
                    [
                        'icon' => 'Award',
                        'title' => 'Premium Quality Control',
                        'desc' => 'Strict quality checks ensure that only fresh, high-grade, chemical-free products reach your doorstep.',
                        'bgClass' => 'bg-amber-50 dark:bg-amber-900/30'
                    ],
                    [
                        'icon' => 'Truck',
                        'title' => 'Nationwide Fast Delivery',
                        'desc' => 'Reliable & non-contact cash-on-delivery across all 64 districts in Bangladesh with express processing.',
                        'bgClass' => 'bg-blue-50 dark:bg-blue-900/30'
                    ],
                    [
                        'icon' => 'HeartHandshake',
                        'title' => 'Customer-First Support',
                        'desc' => 'Dedicated customer service team available 7 days a week to answer your questions and assist your health journey.',
                        'bgClass' => 'bg-amber-50 dark:bg-amber-900/30'
                    ]
                ],
                'stats' => [
                    ['value' => '50,000+', 'label' => 'Happy Customers'],
                    ['value' => '1,200+', 'label' => 'Organic Products'],
                    ['value' => '64', 'label' => 'Districts Covered'],
                    ['value' => '4.9 / 5', 'label' => 'Customer Rating']
                ],
                'team_badge' => 'The Team',
                'team_title' => 'Meet Our Leadership',
                'team_subtitle' => 'Passionate people working every day to bring purity and wellness to your door.',
                'team' => $team,
                'cta_title' => 'Have Questions or Need Recommendations?',
                'cta_subtitle' => 'Our dedicated support team is here to assist you with order inquiries, product guidance, and delivery updates.',
                'cta_phone' => '01681-135030',
                'cta_email' => 'info@shopiabd.com',
            ]
        );
    }
}
