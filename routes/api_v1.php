<?php

use App\Http\Controllers\API\V1\AboutPageController;
use App\Http\Controllers\API\V1\ContactMessageController;
use App\Http\Controllers\API\V1\ContactSettingController;
use App\Http\Controllers\API\V1\FooterSettingController;
use App\Http\Controllers\API\V1\AttributeController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\CustomerController;
use App\Http\Controllers\API\V1\FaqController;
use App\Http\Controllers\API\V1\OrderController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\ReviewController;
use App\Http\Controllers\API\V1\SubscriptionController;
use App\Http\Controllers\API\V1\ChatController;
use App\Http\Controllers\API\V1\BlogController;
use App\Http\Controllers\API\V1\CouponController;
use Illuminate\Support\Facades\Route;

// Health Check API
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running'
    ]);
});

// Public Catalog Routes
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);
Route::get('attributes', [AttributeController::class, 'index']);
Route::get('attributes/{attribute}', [AttributeController::class, 'show']);
Route::get('reviews', [ReviewController::class, 'index']);
Route::get('reviews/{review}', [ReviewController::class, 'show']);
Route::post('reviews', [ReviewController::class, 'store']);
Route::get('faqs', [FaqController::class, 'index']);
Route::get('faqs/{faq}', [FaqController::class, 'show']);
Route::get('faq-categories', [\App\Http\Controllers\API\AdminController::class, 'faqCategoriesIndex']);
Route::get('faq-categories/{id}', [\App\Http\Controllers\API\AdminController::class, 'faqCategoriesShow']);
Route::post('subscriptions', [SubscriptionController::class, 'store']);
Route::get('banners', [\App\Http\Controllers\API\V1\BannerController::class, 'index']);
Route::get('about', [AboutPageController::class, 'index']);
Route::get('contact-settings', [ContactSettingController::class, 'index']);
Route::get('whatsapp-settings', [ContactSettingController::class, 'whatsappIndex']);
Route::post('messages', [ContactMessageController::class, 'store']);
Route::post('orders', [OrderController::class, 'store']);
Route::get('orders/track/{order_number}', [OrderController::class, 'trackOrderPublic']);
Route::get('order-statuses/active', [OrderController::class, 'activeOrderStatusesPublic']);
Route::get('footer-settings', [FooterSettingController::class, 'index']);
Route::get('partners', [\App\Http\Controllers\API\AdminController::class, 'partnersIndex']);
Route::get('chats', [ChatController::class, 'getMessages']);
Route::post('chats', [ChatController::class, 'sendMessage']);

// Public Coupon Validation & Apply API
Route::post('coupons/apply', [CouponController::class, 'apply']);
Route::post('coupons/validate', [CouponController::class, 'apply']);

// Public Blog Routes for Frontend (http://localhost:3000/blog)
Route::get('blogs', [BlogController::class, 'index']);
Route::get('blogs/{slug}', [BlogController::class, 'show']);
Route::get('blog-categories', [BlogController::class, 'categories']);
Route::post('blogs/{slug}/comments', [BlogController::class, 'storeComment']);

// Public Location Routes
Route::get('divisions', [\App\Http\Controllers\API\V1\DivisionController::class, 'index']);
Route::get('divisions/{division}', [\App\Http\Controllers\API\V1\DivisionController::class, 'show']);
Route::get('districts', [\App\Http\Controllers\API\V1\DistrictController::class, 'index']);
Route::get('districts/{district}', [\App\Http\Controllers\API\V1\DistrictController::class, 'show']);
Route::get('thanas', [\App\Http\Controllers\API\V1\ThanaController::class, 'index']);
Route::get('thanas/{thana}', [\App\Http\Controllers\API\V1\ThanaController::class, 'show']);

// Authentication Routes
Route::prefix('auth')->group(function () {
    // Role-wise Authentication Routes
    Route::post('/customer/register', [AuthController::class, 'registerCustomer']);
    Route::post('/customer/login', [AuthController::class, 'loginCustomer']);
    Route::post('/seller/register', [AuthController::class, 'registerSeller']);
    Route::post('/seller/login', [AuthController::class, 'loginSeller']);
    Route::post('/admin/register', [AuthController::class, 'registerAdmin']);
    Route::post('/admin/login', [AuthController::class, 'loginAdmin']);

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/profile', [AuthController::class, 'updateProfile']);
        Route::delete('/profile', [AuthController::class, 'deleteAccount']);
        Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
        Route::post('/delete-account', [AuthController::class, 'deleteAccount']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);

        // Wishlist Routes
        Route::get('wishlist', [\App\Http\Controllers\API\CustomerProfileController::class, 'getWishlist']);
        Route::post('wishlist', [\App\Http\Controllers\API\CustomerProfileController::class, 'addToWishlist']);
        Route::delete('wishlist/{id}', [\App\Http\Controllers\API\CustomerProfileController::class, 'removeFromWishlist']);
        Route::get('customer/wishlist', [\App\Http\Controllers\API\CustomerProfileController::class, 'getWishlist']);
        Route::post('customer/wishlist', [\App\Http\Controllers\API\CustomerProfileController::class, 'addToWishlist']);
        Route::delete('customer/wishlist/{id}', [\App\Http\Controllers\API\CustomerProfileController::class, 'removeFromWishlist']);

        // User & Role management
        Route::apiResource('users', \App\Http\Controllers\API\V1\UserController::class);
        Route::apiResource('roles', \App\Http\Controllers\API\V1\RoleController::class);
        Route::apiResource('permissions', \App\Http\Controllers\API\V1\PermissionController::class)->only(['index', 'store', 'destroy']);

        // Protected Category Routes
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

        // Protected Product Routes
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);

        // Protected Attribute Routes
        Route::apiResource('attributes', AttributeController::class)->except(['index', 'show']);

        // Order Routes
        Route::apiResource('orders', OrderController::class)->except(['store']);

        // Customer Profile Routes
        Route::apiResource('customers', CustomerController::class);

        // Protected Review Routes
        Route::apiResource('reviews', ReviewController::class)->except(['index', 'show']);

        // Protected FAQ Routes
        Route::apiResource('faqs', FaqController::class)->except(['index', 'show']);

        // Protected Subscription Routes
        Route::apiResource('subscriptions', SubscriptionController::class)->except(['store']);

        // Admin-only operations & Settings
        Route::middleware('role:admin')->group(function () {
            // Dashboard Stats
            Route::get('/dashboard/stats', [\App\Http\Controllers\API\AdminController::class, 'dashboardStats']);

            // Banners (Admin)
            Route::post('/banners', [\App\Http\Controllers\API\V1\BannerController::class, 'store']);
            Route::put('/banners/{banner}', [\App\Http\Controllers\API\V1\BannerController::class, 'update']);
            Route::delete('/banners/{banner}', [\App\Http\Controllers\API\V1\BannerController::class, 'destroy']);
            Route::get('/banners/{banner}', [\App\Http\Controllers\API\V1\BannerController::class, 'show']);
            Route::get('/banners', [\App\Http\Controllers\API\V1\BannerController::class, 'index']);

            // About Page (Admin)
            Route::match(['post', 'put'], '/about', [AboutPageController::class, 'update']);

            // Contact Settings (Admin)
            Route::put('/contact-settings', [ContactSettingController::class, 'update']);

            // WhatsApp Settings (Admin)
            Route::put('/whatsapp-settings', [ContactSettingController::class, 'update']);

            // Footer Settings (Admin)
            Route::put('/footer-settings', [FooterSettingController::class, 'update']);

            // Messages (Admin)
            Route::put('messages/bulk', [ContactMessageController::class, 'bulkUpdate']);
            Route::apiResource('messages', ContactMessageController::class)->except(['store']);

            // Chat Management (Admin)
            Route::get('/admin/chats', [ChatController::class, 'adminGetChats']);
            Route::get('/admin/chats/{identifier}', [ChatController::class, 'adminGetChatMessages']);
            Route::post('/admin/chats/reply', [ChatController::class, 'adminSendReply']);
        });
    });
});
