<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReviewApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_post_review_and_save_in_database()
    {
        $category = Category::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'PROD-SKU-1',
            'price' => 100.00,
            'stock' => 10,
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $payload = [
            'product_id' => $product->id,
            'name' => 'John Reviewer',
            'rating' => 5,
            'review' => 'Awesome quality product!',
        ];

        $response = $this->postJson('/api/v1/reviews', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.product_id', $product->id)
            ->assertJsonPath('data.author_name', 'John Reviewer')
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.comment', 'Awesome quality product!');

        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'author_name' => 'John Reviewer',
            'rating' => 5,
            'comment' => 'Awesome quality product!',
        ]);
    }

    public function test_authenticated_user_can_post_review()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $category = Category::create(['name' => 'Test Cat', 'slug' => 'test-cat-auth']);
        $product = Product::create([
            'name' => 'Test Product Auth',
            'slug' => 'test-product-auth',
            'sku' => 'PROD-SKU-AUTH',
            'price' => 120.00,
            'stock' => 10,
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $payload = [
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Excellent quality and fast delivery!',
        ];

        $response = $this->postJson('/api/reviews', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.product_id', $product->id)
            ->assertJsonPath('data.author_name', $user->name)
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.comment', 'Excellent quality and fast delivery!');

        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'Excellent quality and fast delivery!',
        ]);
    }

    public function test_everyone_can_fetch_product_reviews()
    {
        $category = Category::create(['name' => 'Test Cat 2', 'slug' => 'test-cat-2']);
        $product = Product::create([
            'name' => 'Test Product 2',
            'slug' => 'test-product-2',
            'sku' => 'PROD-SKU-2',
            'price' => 150.00,
            'stock' => 5,
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        Review::create([
            'product_id' => $product->id,
            'author_name' => 'Jane Smith',
            'rating' => 4,
            'comment' => 'Very satisfied with this purchase.',
            'status' => 'approved',
        ]);

        $response = $this->getJson('/api/v1/reviews?product_id=' . $product->id);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.data.0.author_name', 'Jane Smith')
            ->assertJsonPath('data.data.0.rating', 4);
    }
}
