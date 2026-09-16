<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_manage_blog_posts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $category = BlogCategory::create([
            'name' => 'Tech News',
            'slug' => 'tech-news',
            'status' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.blogs.store'), [
            'title' => 'The Future of AI in E-Commerce',
            'blog_category_id' => $category->id,
            'author_name' => 'Editor in Chief',
            'image' => 'https://example.com/ai.jpg',
            'short_description' => 'A brief glimpse into future AI technology.',
            'content' => '<p>AI is transforming online shopping experiences...</p>',
            'status' => 'published',
            'featured' => 1,
        ]);

        $response->assertRedirect(route('admin.blogs.index'));
        $this->assertDatabaseHas('blogs', [
            'title' => 'The Future of AI in E-Commerce',
            'slug' => 'the-future-of-ai-in-e-commerce',
            'status' => 'published',
            'featured' => 1,
        ]);
    }

    public function test_public_api_returns_published_blogs_for_frontend(): void
    {
        $category = BlogCategory::create([
            'name' => 'Shopping Guides',
            'slug' => 'shopping-guides',
            'status' => true,
        ]);

        $blog = Blog::create([
            'title' => 'Best Deals for Summer 2026',
            'slug' => 'best-deals-for-summer-2026',
            'blog_category_id' => $category->id,
            'author_name' => 'ShopiaBD Deals',
            'content' => '<p>Check out our summer deals!</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/blogs');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'title' => 'Best Deals for Summer 2026',
                'slug' => 'best-deals-for-summer-2026',
            ]);

        // Detail endpoint by slug
        $detailResponse = $this->getJson('/api/v1/blogs/best-deals-for-summer-2026');
        $detailResponse->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Best Deals for Summer 2026',
            ]);
    }
}
