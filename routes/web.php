<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\WishlistController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\AboutPageController;
use App\Http\Controllers\Admin\ContactSettingController;
use App\Http\Controllers\Admin\WhatsAppSettingController;
use App\Http\Controllers\Admin\FooterSettingController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ServiceController;

// Root redirect to admin
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Admin Guest Routes (Auth)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.post');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Protected Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Products
        Route::delete('/products/images/{id}', [ProductController::class, 'deleteImageItem'])->name('products.images.delete');
        Route::post('/products/images/{id}/primary', [ProductController::class, 'setPrimaryImage'])->name('products.images.primary');
        Route::post('/products/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::resource('products', ProductController::class);

        // Categories & Sub Categories
        Route::resource('categories', CategoryController::class);
        Route::resource('sub-categories', SubCategoryController::class);

        // Attributes
        Route::resource('attributes', AttributeController::class);

        // Orders & Order Statuses
        Route::get('/orders/payment-status', [OrderController::class, 'paymentStatus'])->name('orders.payment-status');
        Route::post('/orders/payment-status', [OrderController::class, 'storePaymentStatus'])->name('orders.payment-status.store');
        Route::put('/orders/payment-status/{id}', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment-status.update');
        Route::delete('/orders/payment-status/{id}', [OrderController::class, 'destroyPaymentStatus'])->name('orders.payment-status.destroy');

        Route::get('/orders/order-status', [OrderController::class, 'orderStatus'])->name('orders.order-status');
        Route::post('/orders/order-status', [OrderController::class, 'storeOrderStatus'])->name('orders.order-status.store');
        Route::put('/orders/order-status/{id}', [OrderController::class, 'updateOrderStatus'])->name('orders.order-status.update');
        Route::delete('/orders/order-status/{id}', [OrderController::class, 'destroyOrderStatus'])->name('orders.order-status.destroy');

        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::resource('orders', OrderController::class)->only(['index', 'show']);

        // Coupons
        Route::get('/coupons/generate-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
        Route::post('/coupons/{id}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
        Route::resource('coupons', CouponController::class);

        // Customers
        Route::post('/customers/{id}/toggle-block', [CustomerController::class, 'toggleBlock'])->name('customers.toggle-block');
        Route::resource('customers', CustomerController::class)->only(['index', 'show', 'create', 'store', 'destroy']);

        // Partners & Brands
        Route::resource('partners', PartnerController::class);

        // Messages
        Route::post('/messages/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('messages.mark-all-read');
        Route::post('/messages/{id}/toggle-status', [MessageController::class, 'toggleStatus'])->name('messages.toggle-status');
        Route::resource('messages', MessageController::class)->only(['index', 'destroy']);

        // Live Chat Support
        Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
        Route::get('/chats/{sessionId}/fetch', [ChatController::class, 'fetchMessages'])->name('chats.fetch');
        Route::post('/chats/reply', [ChatController::class, 'reply'])->name('chats.reply');
        Route::post('/chats/toggle-block', [ChatController::class, 'toggleBlock'])->name('chats.toggle-block');
        Route::post('/chats/toggle-approve', [ChatController::class, 'toggleApprove'])->name('chats.toggle-approve');

        // Reviews
        Route::post('/reviews/{id}/toggle-status', [ReviewController::class, 'toggleStatus'])->name('reviews.toggle-status');
        Route::resource('reviews', ReviewController::class);

        // Wishlists
        Route::post('/wishlist/{id}/notify', [WishlistController::class, 'notifyUser'])->name('wishlist.notify');
        Route::resource('wishlist', WishlistController::class)->only(['index', 'destroy']);

        // FAQs & FAQ Categories
        Route::resource('faq-categories', FaqCategoryController::class);
        Route::resource('faqs', FaqController::class);

        // Subscriptions
        Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'destroy']);

        // Banners
        Route::post('/banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
        Route::post('/banners/{id}/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
        Route::resource('banners', BannerController::class);

        // Website Content & Settings
        Route::get('/about', [AboutPageController::class, 'index'])->name('about.index');
        Route::post('/about', [AboutPageController::class, 'update'])->name('about.update');

        Route::get('/contact-settings', [ContactSettingController::class, 'index'])->name('contact-settings.index');
        Route::post('/contact-settings', [ContactSettingController::class, 'update'])->name('contact-settings.update');

        Route::get('/whatsapp-settings', [WhatsAppSettingController::class, 'index'])->name('whatsapp-settings.index');
        Route::post('/whatsapp-settings', [WhatsAppSettingController::class, 'update'])->name('whatsapp-settings.update');

        Route::get('/footer-settings', [FooterSettingController::class, 'index'])->name('footer-settings.index');
        Route::post('/footer-settings', [FooterSettingController::class, 'update'])->name('footer-settings.update');

        // Location Management
        Route::get('/locations/divisions', [LocationController::class, 'divisions'])->name('locations.divisions');
        Route::post('/locations/divisions', [LocationController::class, 'storeDivision'])->name('locations.divisions.store');
        Route::delete('/locations/divisions/{id}', [LocationController::class, 'destroyDivision'])->name('locations.divisions.destroy');

        Route::get('/locations/districts', [LocationController::class, 'districts'])->name('locations.districts');
        Route::post('/locations/districts', [LocationController::class, 'storeDistrict'])->name('locations.districts.store');
        Route::delete('/locations/districts/{id}', [LocationController::class, 'destroyDistrict'])->name('locations.districts.destroy');

        Route::get('/locations/thanas', [LocationController::class, 'thanas'])->name('locations.thanas');
        Route::post('/locations/thanas', [LocationController::class, 'storeThana'])->name('locations.thanas.store');
        Route::delete('/locations/thanas/{id}', [LocationController::class, 'destroyThana'])->name('locations.thanas.destroy');

        // User & Staff Management
        Route::match(['get', 'post'], '/users/{id}/update-status', [UserController::class, 'updateStatus'])->name('users.update-status');
        Route::post('/users/{id}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{id}/reject', [UserController::class, 'reject'])->name('users.reject');
        Route::post('/users/{id}/toggle-block', [UserController::class, 'toggleBlock'])->name('users.toggle-block');
        Route::resource('users', UserController::class);

        // General System Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::get('/settings/profile', [SettingController::class, 'profile'])->name('settings.profile');
        Route::post('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');

        // Blog Management System
        Route::get('/blogs/categories/manage', [BlogController::class, 'categories'])->name('blogs.categories');
        Route::post('/blogs/categories/manage', [BlogController::class, 'storeCategory'])->name('blogs.categories.store');
        Route::put('/blogs/categories/manage/{id}', [BlogController::class, 'updateCategory'])->name('blogs.categories.update');
        Route::delete('/blogs/categories/manage/{id}', [BlogController::class, 'destroyCategory'])->name('blogs.categories.destroy');

        Route::get('/blogs/comments/manage', [BlogController::class, 'comments'])->name('blogs.comments');
        Route::post('/blogs/comments/{id}/approve', [BlogController::class, 'approveComment'])->name('blogs.comments.approve');
        Route::delete('/blogs/comments/{id}', [BlogController::class, 'destroyComment'])->name('blogs.comments.destroy');

        Route::resource('blogs', BlogController::class);

        // Our Services Management System
        Route::post('/services/{id}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
        Route::resource('services', ServiceController::class);
    });
});
