<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $category1 = BlogCategory::firstOrCreate(
            ['slug' => 'shopping-guides'],
            ['name' => 'Shopping Guides', 'description' => 'Buying guides, product tips, and reviews.', 'status' => true]
        );

        $category2 = BlogCategory::firstOrCreate(
            ['slug' => 'tech-trends'],
            ['name' => 'Tech & Trends', 'description' => 'Latest trends in electronics, gadgets, and lifestyle.', 'status' => true]
        );

        $category3 = BlogCategory::firstOrCreate(
            ['slug' => 'promotions'],
            ['name' => 'Promotions & Deals', 'description' => 'Special sales, coupon codes, and store news.', 'status' => true]
        );

        Blog::firstOrCreate(
            ['slug' => 'top-10-must-have-gadgets-in-2026'],
            [
                'title' => 'Top 10 Must-Have Tech Gadgets in 2026',
                'blog_category_id' => $category2->id,
                'author_name' => 'ShopiaBD Team',
                'image' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80',
                'short_description' => 'Discover the most innovative smart gadgets and accessories that elevate your productivity and everyday lifestyle this year.',
                'content' => '<p>Technology evolves rapidly, and staying ahead of the curve makes daily life seamless and productive. In this comprehensive guide, we review the top 10 groundbreaking gadgets of 2026.</p><h3>1. Smart ANC Headphones</h3><p>Active Noise Cancellation has reached new heights with AI sound isolation technology.</p><h3>2. High-Speed Wireless Charging Docks</h3><p>Streamline your desk with multi-device fast magnetic chargers.</p>',
                'views' => 124,
                'status' => 'published',
                'featured' => true,
                'published_at' => now(),
            ]
        );

        Blog::firstOrCreate(
            ['slug' => 'how-to-choose-the-perfect-smartwatch'],
            [
                'title' => 'How to Choose the Perfect Smartwatch for Your Fitness Goals',
                'blog_category_id' => $category1->id,
                'author_name' => 'Sarah Johnson',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=1200&q=80',
                'short_description' => 'Confused by all the wearable options? Here is a step-by-step buyer guide to picking the right smartwatch for battery life, GPS, and health metrics.',
                'content' => '<p>Smartwatches are no longer just fitness trackers; they are personal health assistants on your wrist. Whether you are an athlete or casual walker, selecting the right smartwatch depends on display quality, sensor accuracy, and battery efficiency.</p>',
                'views' => 89,
                'status' => 'published',
                'featured' => false,
                'published_at' => now()->subDays(2),
            ]
        );

        Blog::firstOrCreate(
            ['slug' => 'exclusive-summer-sale-announcement'],
            [
                'title' => 'Exclusive Grand Sale: Up to 50% Off Top Electronics',
                'blog_category_id' => $category3->id,
                'author_name' => 'ShopiaBD Deals',
                'image' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&w=1200&q=80',
                'short_description' => 'Get ready for our biggest sale event of the season! Massive discounts across audio gear, smart devices, and home appliances.',
                'content' => '<p>We are thrilled to announce our Summer Tech Sale! For a limited time only, enjoy up to 50% off select products and free shipping on all orders over ৳1,500.</p>',
                'views' => 210,
                'status' => 'published',
                'featured' => true,
                'published_at' => now()->subDays(5),
            ]
        );
    }
}
