<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\ProductImage;
use App\Models\Partner;
use App\Models\FAQCategory;
use App\Models\PaymentStatus;
use App\Models\OrderStatus;
use App\Traits\UploadImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    use UploadImageTrait;

    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function dashboard(): JsonResponse
    {
        // 1. Core counters
        $totalUsers = \App\Models\User::count();
        $totalCustomers = \App\Models\User::where('role', 'customer')->count();
        $totalProducts = \App\Models\Product::count();
        $totalCategories = \App\Models\Category::count();
        $totalOrders = \App\Models\Order::count();
        
        // 2. Active orders and revenue
        $activeOrders = \App\Models\Order::where('status', '!=', 'cancelled')->get();
        $totalRevenue = $activeOrders->sum('total');

        // 3. Recent 5 orders
        $recentOrders = \App\Models\Order::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customerName' => $order->customer_name ?: ($order->user ? $order->user->name : 'Guest'),
                    'customerEmail' => $order->customer_email ?: ($order->user ? $order->user->email : 'N/A'),
                    'amount' => (float) $order->total,
                    'status' => ucfirst($order->status),
                    'date' => $order->created_at ? $order->created_at->format('Y-m-d H:i') : 'N/A',
                ];
            });

        // 4. Top 3 Selling Products
        $topSelling = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as sales_count'), \DB::raw('SUM(price * quantity) as revenue'))
            ->groupBy('product_id')
            ->orderByDesc('revenue')
            ->take(3)
            ->with('product.images')
            ->get()
            ->map(function ($item) {
                $p = $item->product;
                if (!$p) return null;
                $imageUrl = $p->image ?: ($p->images->first() ? $p->images->first()->image_path : '');
                $formattedImage = $imageUrl 
                    ? (str_starts_with($imageUrl, 'data:image') ? $imageUrl : url(\Storage::url($imageUrl)))
                    : 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 100 100"><rect width="100" height="100" fill="%2316A34A"/></svg>';
                
                return [
                    'name' => $p->name,
                    'revenue' => '৳' . number_format($item->revenue, 2),
                    'salesCount' => (int) $item->sales_count,
                    'image' => str_starts_with($formattedImage, 'data:image') 
                        ? '<img src="' . $formattedImage . '" class="h-full w-full object-cover" />'
                        : '<img src="' . $formattedImage . '" class="h-full w-full object-cover" />',
                ];
            })->filter()->values();

        // 5. Chart Data (Monthly revenue trend for last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            $rev = (float) \App\Models\Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total');
                
            $monthlyRevenue[] = [
                'name' => $month->format('M'),
                'Revenue' => $rev,
                'Expenses' => round($rev * 0.65), 
            ];
        }

        // 6. Chart Data (Weekly sales count per day for current week)
        $weeklySales = [];
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach ($days as $idx => $dayName) {
            $targetDay = now()->startOfWeek()->addDays($idx);
            $salesCount = (int) \App\Models\Order::whereDate('created_at', $targetDay)->count();
            $weeklySales[] = [
                'name' => $dayName,
                'Sales' => $salesCount,
            ];
        }

        // 7. Chart Data (Orders count trend for last 7 days)
        $ordersTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = (int) \App\Models\Order::whereDate('created_at', $date)->count();
            $ordersTrend[] = [
                'name' => $date->format('M d'),
                'Orders' => $count,
            ];
        }

        return $this->success([
            'stats' => [
                'total_revenue' => (float) $totalRevenue,
                'total_orders' => (int) $totalOrders,
                'total_products' => (int) $totalProducts,
                'total_customers' => (int) $totalCustomers,
            ],
            'recent_orders' => $recentOrders,
            'top_products' => $topSelling,
            'charts' => [
                'revenue_data' => $monthlyRevenue,
                'sales_data' => $weeklySales,
                'orders_trend_data' => $ordersTrend,
            ]
        ], 'Dashboard stats retrieved successfully');
    }

    // Product Management
    public function productsIndex(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = min($perPage, 200); // cap at 200 to avoid memory issues
        $products = Product::with(['category', 'subCategory', 'images'])->latest('id')->paginate($perPage);
        return $this->success($products, 'Products retrieved successfully');
    }


    public function productsStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'SKU' => 'required|string|unique:products',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'nullable|boolean',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'sub_category' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'tax' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0|max:100',
            'unit' => 'nullable|string|max:50',
            'stock_status' => 'nullable|string|max:50',
            'featured' => 'nullable|boolean',
            'best_seller' => 'nullable|boolean',
            'organic' => 'nullable|boolean',
            'new_arrival' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'attributes' => 'nullable|array',
        ]);

        if (empty($validated['sub_category_id']) && !empty($validated['sub_category'])) {
            $subCatObj = SubCategory::where('name', $validated['sub_category'])
                ->orWhere('id', $validated['sub_category'])
                ->first();
            if ($subCatObj) {
                $validated['sub_category_id'] = $subCatObj->id;
                $validated['sub_category'] = $subCatObj->name;
            }
        } elseif (!empty($validated['sub_category_id']) && empty($validated['sub_category'])) {
            $subCatObj = SubCategory::find($validated['sub_category_id']);
            if ($subCatObj) {
                $validated['sub_category'] = $subCatObj->name;
            }
        }

        $galleryPaths = [];
        $galleryInput = $request->input('gallery', []);
        if (is_array($galleryInput)) {
            foreach ($galleryInput as $imgItem) {
                if (empty($imgItem)) continue;
                if (str_starts_with($imgItem, 'data:image/')) {
                    $galleryPaths[] = $this->uploadBase64Image($imgItem, 'products/gallery');
                } else {
                    $path = $imgItem;
                    if (str_contains($imgItem, '/storage/')) {
                        $path = substr($imgItem, strpos($imgItem, '/storage/') + 9);
                    }
                    $galleryPaths[] = $path;
                }
            }
        }

        $mainImagePath = !empty($galleryPaths) ? $galleryPaths[0] : null;

        unset($validated['image'], $validated['gallery']);
        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        $product = Product::create($validated);

        if ($product && !empty($galleryPaths)) {
            foreach ($galleryPaths as $path) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        return $this->success($product->load(['category', 'subCategory', 'images']), 'Product created successfully', 201);
    }

    public function productsShow(string $id): JsonResponse
    {
        $product = Product::with(['category', 'subCategory', 'images'])->findOrFail($id);
        return $this->success($product, 'Product details retrieved');
    }

    public function productsUpdate(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'SKU' => 'nullable|string|unique:products,SKU,' . $id,
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'nullable|boolean',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'sub_category' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'tax' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0|max:100',
            'unit' => 'nullable|string|max:50',
            'stock_status' => 'nullable|string|max:50',
            'featured' => 'nullable|boolean',
            'best_seller' => 'nullable|boolean',
            'organic' => 'nullable|boolean',
            'new_arrival' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string|max:255',
            'attributes' => 'nullable|array',
        ]);

        if (empty($validated['sub_category_id']) && !empty($validated['sub_category'])) {
            $subCatObj = SubCategory::where('name', $validated['sub_category'])
                ->orWhere('id', $validated['sub_category'])
                ->first();
            if ($subCatObj) {
                $validated['sub_category_id'] = $subCatObj->id;
                $validated['sub_category'] = $subCatObj->name;
            }
        } elseif (!empty($validated['sub_category_id']) && empty($validated['sub_category'])) {
            $subCatObj = SubCategory::find($validated['sub_category_id']);
            if ($subCatObj) {
                $validated['sub_category'] = $subCatObj->name;
            }
        }

        $galleryPaths = [];
        $galleryInput = $request->input('gallery', []);
        if (is_array($galleryInput)) {
            foreach ($galleryInput as $imgItem) {
                if (empty($imgItem)) continue;
                if (str_starts_with($imgItem, 'data:image/')) {
                    $galleryPaths[] = $this->uploadBase64Image($imgItem, 'products/gallery');
                } else {
                    $path = $imgItem;
                    if (str_contains($imgItem, '/storage/')) {
                        $path = substr($imgItem, strpos($imgItem, '/storage/') + 9);
                    }
                    $galleryPaths[] = $path;
                }
            }
        }

        $mainImagePath = !empty($galleryPaths) ? $galleryPaths[0] : null;

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        }

        unset($validated['image'], $validated['gallery']);
        $product->update(array_filter($validated, function ($val) {
            return $val !== null;
        }));

        ProductImage::where('product_id', $product->id)->delete();
        foreach ($galleryPaths as $path) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }

        return $this->success($product->load('images'), 'Product updated successfully');
    }


    public function productsDestroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return $this->success([], 'Product deleted successfully');
    }

    // Category Management
    public function categoriesIndex(): JsonResponse
    {
        $categories = Category::with(['parent', 'children', 'subCategories'])->get();
        return $this->success($categories, 'Categories retrieved successfully');
    }

    public function categoriesStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->uploadImage($request->file('image_file'), 'categories');
        } elseif (!empty($validated['image'])) {
            if (str_starts_with($validated['image'], 'data:image/') || strlen($validated['image']) > 100) {
                $validated['image'] = $this->uploadBase64Image($validated['image'], 'categories');
            }
        }

        unset($validated['image_file']);

        $category = Category::create($validated);

        // Also if parent_id is provided, create a record in sub_categories table for full compatibility
        if (!empty($validated['parent_id'])) {
            \App\Models\SubCategory::create([
                'category_id' => $validated['parent_id'],
                'name'        => $category->name,
                'description' => $category->description,
                'image'       => $category->image,
                'status'      => $category->status,
            ]);
        }

        return $this->success($category, 'Category created successfully', 201);
    }

    public function categoriesShow(string $id): JsonResponse
    {
        $category = Category::with(['parent', 'children', 'subCategories'])->findOrFail($id);
        return $this->success($category, 'Category details retrieved');
    }

    public function categoriesUpdate(Request $request, string $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->uploadImage($request->file('image_file'), 'categories');
        } elseif (!empty($validated['image'])) {
            if (str_starts_with($validated['image'], 'data:image/') || strlen($validated['image']) > 100) {
                $validated['image'] = $this->uploadBase64Image($validated['image'], 'categories');
            }
        }

        unset($validated['image_file']);

        $category->update(array_filter($validated, function ($val) {
            return $val !== null;
        }));
        return $this->success($category, 'Category updated successfully');
    }

    public function categoriesDestroy(string $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return $this->success([], 'Category deleted successfully');
    }

    // Sub Category Management (SubCategory model & table)
    public function subCategoriesIndex(): JsonResponse
    {
        $subCategories = \App\Models\SubCategory::with('category')->latest()->get();
        return $this->success($subCategories, 'Sub categories retrieved successfully');
    }

    public function subCategoriesStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'image_file'  => 'nullable|image|max:5120',
            'status'      => 'nullable|boolean',
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->uploadImage($request->file('image_file'), 'sub_categories');
        } elseif (!empty($validated['image'])) {
            if (str_starts_with($validated['image'], 'data:image/') || strlen($validated['image']) > 100) {
                $validated['image'] = $this->uploadBase64Image($validated['image'], 'sub_categories');
            }
        }

        unset($validated['image_file']);

        $subCategory = \App\Models\SubCategory::create($validated);

        return $this->success($subCategory->load('category'), 'Sub category created successfully', 201);
    }

    public function subCategoriesShow(string $id): JsonResponse
    {
        $subCategory = \App\Models\SubCategory::with('category')->findOrFail($id);
        return $this->success($subCategory, 'Sub category details retrieved');
    }

    public function subCategoriesUpdate(Request $request, string $id): JsonResponse
    {
        $subCategory = \App\Models\SubCategory::findOrFail($id);
        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|string',
            'image_file'  => 'nullable|image|max:5120',
            'status'      => 'nullable|boolean',
        ]);

        if (empty($validated['category_id'])) {
            unset($validated['category_id']);
        }

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->uploadImage($request->file('image_file'), 'sub_categories');
        } elseif (!empty($validated['image'])) {
            if (str_starts_with($validated['image'], 'data:image/') || strlen($validated['image']) > 100) {
                $validated['image'] = $this->uploadBase64Image($validated['image'], 'sub_categories');
            }
        }

        unset($validated['image_file']);

        $updateData = array_filter($validated, function ($val) {
            return $val !== null && $val !== '';
        });

        $subCategory->update($updateData);

        return $this->success($subCategory->load('category'), 'Sub category updated successfully');
    }

    public function subCategoriesDestroy(string $id): JsonResponse
    {
        $subCategory = \App\Models\SubCategory::findOrFail($id);
        $subCategory->delete();
        return $this->success([], 'Sub category deleted successfully');
    }

    // FAQ Category Management
    public function faqCategoriesIndex(): JsonResponse
    {
        $faqCategories = FAQCategory::all();
        return $this->success($faqCategories, 'FAQ Categories retrieved successfully');
    }

    public function faqCategoriesStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $faqCategory = FAQCategory::create($validated);

        return $this->success($faqCategory, 'FAQ Category created successfully', 201);
    }

    public function faqCategoriesShow(string $id): JsonResponse
    {
        $faqCategory = FAQCategory::findOrFail($id);
        return $this->success($faqCategory, 'FAQ Category details retrieved');
    }

    public function faqCategoriesUpdate(Request $request, string $id): JsonResponse
    {
        $faqCategory = FAQCategory::findOrFail($id);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $faqCategory->update(array_filter($validated, function ($val) {
            return $val !== null;
        }));
        return $this->success($faqCategory, 'FAQ Category updated successfully');
    }

    public function faqCategoriesDestroy(string $id): JsonResponse
    {
        $faqCategory = FAQCategory::findOrFail($id);
        $faqCategory->delete();
        return $this->success([], 'FAQ Category deleted successfully');
    }

    // Partner Management
    public function partnersIndex(): JsonResponse
    {
        $partners = Partner::all();
        return $this->success($partners, 'Partners retrieved successfully');
    }

    public function partnersStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|string|url|max:255',
            'logo' => 'nullable|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        if (!empty($validated['logo'])) {
            if (str_starts_with($validated['logo'], 'data:image/') || strlen($validated['logo']) > 100) {
                $validated['logo'] = $this->uploadBase64Image($validated['logo'], 'partners/logos');
            }
        }

        if (!empty($validated['image'])) {
            if (str_starts_with($validated['image'], 'data:image/') || strlen($validated['image']) > 100) {
                $validated['image'] = $this->uploadBase64Image($validated['image'], 'partners/images');
            }
        }

        $partner = Partner::create($validated);

        return $this->success($partner, 'Partner created successfully', 201);
    }

    public function partnersShow(string $id): JsonResponse
    {
        $partner = Partner::findOrFail($id);
        return $this->success($partner, 'Partner details retrieved');
    }

    public function partnersUpdate(Request $request, string $id): JsonResponse
    {
        $partner = Partner::findOrFail($id);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'website' => 'nullable|string|url|max:255',
            'logo' => 'nullable|string',
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        if (!empty($validated['logo'])) {
            if (str_starts_with($validated['logo'], 'data:image/') || strlen($validated['logo']) > 100) {
                $validated['logo'] = $this->uploadBase64Image($validated['logo'], 'partners/logos');
            }
        }

        if (!empty($validated['image'])) {
            if (str_starts_with($validated['image'], 'data:image/') || strlen($validated['image']) > 100) {
                $validated['image'] = $this->uploadBase64Image($validated['image'], 'partners/images');
            }
        }

        $partner->update(array_filter($validated, function ($val) {
            return $val !== null;
        }));

        return $this->success($partner, 'Partner updated successfully');
    }

    public function partnersDestroy(string $id): JsonResponse
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();
        return $this->success([], 'Partner deleted successfully');
    }

    // Brand Management
    public function brandsIndex(): JsonResponse
    {
        $brands = $this->adminService->paginateBrands(50);
        return $this->success($brands, 'Brands retrieved successfully');
    }

    public function brandsStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $brand = $this->adminService->createBrand($validated);

        return $this->success($brand, 'Brand created successfully', 201);
    }

    public function brandsUpdate(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'logo' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $this->adminService->updateBrand($id, array_filter($validated));
        return $this->success([], 'Brand updated successfully');
    }

    public function brandsDestroy(string $id): JsonResponse
    {
        $this->adminService->deleteBrand($id);
        return $this->success([], 'Brand deleted successfully');
    }

    // Order Management
    public function ordersIndex(): JsonResponse
    {
        $orders = Order::with('user')->latest()->paginate(15);
        return $this->success($orders, 'Orders retrieved successfully');
    }

    public function ordersShow(string $id): JsonResponse
    {
        $order = Order::with('user', 'items.product')->findOrFail($id);
        return $this->success($order, 'Order details retrieved');
    }

    public function ordersUpdate(Request $request, string $id): JsonResponse
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|string',
            'payment_status' => 'nullable|string',
        ]);

        $order->update($validated);
        return $this->success($order, 'Order updated successfully');
    }

    public function ordersUpdateStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $order->update(['status' => $validated['status']]);

        return $this->success($order, 'Order status updated successfully');
    }

    // Customer Management
    public function customersIndex(): JsonResponse
    {
        $customers = $this->adminService->paginateCustomers();
        return $this->success($customers, 'Customers list retrieved');
    }

    public function customersStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:6',
            'profile_pic' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        if (!empty($validated['profile_pic'])) {
            if (str_starts_with($validated['profile_pic'], 'data:image/') || strlen($validated['profile_pic']) > 100) {
                $validated['profile_pic'] = $this->uploadBase64Image($validated['profile_pic'], 'users/profiles');
            }
        }

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
            'role' => 'customer',
            'status' => $validated['status'] ?? 'active',
            'profile_pic' => $validated['profile_pic'] ?? null,
        ]);
        
        \App\Models\CustomerProfile::create(['user_id' => $user->id]);

        return $this->success($user, 'Customer created successfully', 201);
    }

    public function customersShow(string $id): JsonResponse
    {
        $customer = $this->adminService->getCustomerDetails($id);
        return $this->success($customer, 'Customer details retrieved');
    }

    public function customersUpdate(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_pic' => 'nullable|string',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['profile_pic'])) {
            if (str_starts_with($validated['profile_pic'], 'data:image/') || strlen($validated['profile_pic']) > 100) {
                $validated['profile_pic'] = $this->uploadBase64Image($validated['profile_pic'], 'users/profiles');
            }
        }

        if (!empty($validated['password'])) {
            $user = \App\Models\User::findOrFail($id);
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Current password does not match'], 400);
            }
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }
        unset($validated['current_password']);

        $this->adminService->updateCustomer($id, array_filter($validated));
        return $this->success([], 'Customer updated successfully');
    }

    public function customersDestroy(string $id): JsonResponse
    {
        $this->adminService->deleteUser($id);
        return $this->success([], 'Customer deleted successfully');
    }

    public function customersToggleBlock(string $id): JsonResponse
    {
        $user = $this->adminService->toggleCustomerBlock($id);
        return $this->success($user, "Customer block status toggled. Current status: {$user->status}");
    }

    // General Users CRUD
    public function usersIndex(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 15);
        $users = $this->adminService->paginateUsers($perPage);
        return $this->success($users, 'Users retrieved successfully');
    }

    public function usersUpdate(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|string|in:admin,customer,vendor,staff',
            'status' => 'nullable|string|in:active,blocked',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = $this->adminService->updateUser($id, $validated);
        return $this->success($user, 'User updated successfully');
    }

    public function usersDestroy(string $id): JsonResponse
    {
        $this->adminService->deleteUser($id);
        return $this->success([], 'User deleted successfully');
    }

    // Payment Status Management
    public function paymentStatusesIndex(Request $request): JsonResponse
    {
        if ($request->boolean('all')) {
            $statuses = PaymentStatus::orderBy('id', 'desc')->get();
            return $this->success($statuses, 'Payment statuses retrieved successfully');
        }
        $perPage = $request->query('per_page', 15);
        $statuses = PaymentStatus::orderBy('id', 'desc')->paginate($perPage);
        return $this->success($statuses, 'Payment statuses retrieved successfully');
    }

    public function paymentStatusesStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_statuses,name',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $status = PaymentStatus::create($validated);

        return $this->success($status, 'Payment status created successfully', 201);
    }

    public function paymentStatusesShow(string $id): JsonResponse
    {
        $status = PaymentStatus::findOrFail($id);
        return $this->success($status, 'Payment status details retrieved');
    }

    public function paymentStatusesUpdate(Request $request, string $id): JsonResponse
    {
        $status = PaymentStatus::findOrFail($id);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255|unique:payment_statuses,name,' . $id,
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $status->update(array_filter($validated, function ($val) {
            return $val !== null;
        }));

        return $this->success($status, 'Payment status updated successfully');
    }

    public function paymentStatusesDestroy(string $id): JsonResponse
    {
        $status = PaymentStatus::findOrFail($id);
        $status->delete();
        return $this->success([], 'Payment status deleted successfully');
    }

    // Order Status Management
    public function orderStatusesIndex(Request $request): JsonResponse
    {
        if ($request->boolean('all')) {
            $statuses = OrderStatus::orderBy('id', 'asc')->get();
            return $this->success($statuses, 'Order statuses retrieved successfully');
        }
        $perPage = $request->query('per_page', 15);
        $statuses = OrderStatus::orderBy('id', 'asc')->paginate($perPage);
        return $this->success($statuses, 'Order statuses retrieved successfully');
    }

    public function orderStatusesStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:order_statuses,name',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $status = OrderStatus::create($validated);

        return $this->success($status, 'Order status created successfully', 201);
    }

    public function orderStatusesShow(string $id): JsonResponse
    {
        $status = OrderStatus::findOrFail($id);
        return $this->success($status, 'Order status details retrieved');
    }

    public function orderStatusesUpdate(Request $request, string $id): JsonResponse
    {
        $status = OrderStatus::findOrFail($id);
        $validated = $request->validate([
            'name' => 'nullable|string|max:255|unique:order_statuses,name,' . $id,
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $status->update(array_filter($validated, function ($val) {
            return $val !== null;
        }));

        return $this->success($status, 'Order status updated successfully');
    }

    public function orderStatusesDestroy(string $id): JsonResponse
    {
        $status = OrderStatus::findOrFail($id);
        $status->delete();
        return $this->success([], 'Order status deleted successfully');
    }

    public function wishlistsIndex(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = min($perPage, 200);
        $wishlists = \App\Models\Wishlist::with(['user', 'product.images'])->paginate($perPage);
        return $this->success(
            \App\Http\Resources\API\V1\WishlistResource::collection($wishlists)->response()->getData(true),
            'All wishlists retrieved successfully'
        );
    }

    public function wishlistsDestroy(string $id): JsonResponse
    {
        $wishlist = \App\Models\Wishlist::findOrFail($id);
        $wishlist->delete();
        return $this->success([], 'Wishlist item deleted successfully');
    }

    public function wishlistsNotify(Request $request, string $id): JsonResponse
    {
        $wishlist = \App\Models\Wishlist::with(['user', 'product'])->findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'send_email' => 'nullable|boolean',
            'send_chat' => 'nullable|boolean',
        ]);

        $user = $wishlist->user;
        $product = $wishlist->product;

        if (!$user) {
            return $this->error('Customer associated with this wishlist item could not be found', 404);
        }

        $subject = $validated['subject'];
        $message = $validated['message'];
        $sendEmail = $request->boolean('send_email', true);
        $sendChat = $request->boolean('send_chat', true);

        $channels = [];

        if ($sendEmail && $user->email) {
            try {
                $actionUrl = config('app.url') . '/products/' . ($product->id ?? '');
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WishlistNotificationMail($user, $product, $subject, $message, $actionUrl));
                $channels[] = 'Email';
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Wishlist Mail Error (API): ' . $e->getMessage());
                $channels[] = 'Email (Queued)';
            }
        }

        if ($sendChat) {
            try {
                $admin = \Illuminate\Support\Facades\Auth::user();
                $sessionId = 'user_' . $user->id;
                \App\Models\ChatMessage::create([
                    'session_id' => $sessionId,
                    'user_id' => $user->id,
                    'admin_id' => $admin ? $admin->id : null,
                    'sender' => 'admin',
                    'message' => "📢 [Wishlist Alert - " . ($product->name ?? 'Item') . "]\n" . $subject . "\n\n" . $message,
                    'is_read' => false,
                ]);
                $channels[] = 'Live Chat';
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Wishlist Chat Error (API): ' . $e->getMessage());
            }
        }

        $wishlist->update(['last_notified_at' => now()]);

        return $this->success([
            'wishlist_id' => $wishlist->id,
            'last_notified_at' => $wishlist->last_notified_at,
            'channels' => $channels,
        ], 'Notification sent successfully');
    }
}
