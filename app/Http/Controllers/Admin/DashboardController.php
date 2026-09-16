<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Core Metrics
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // 2. Monthly Revenue (Past 6 Months)
        $revenueLabels = [];
        $revenueValues = [];
        $expenseValues = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $rev = (float) Order::where('status', '!=', 'cancelled')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total');

            $revenueLabels[] = $month->format('M');
            $revenueValues[] = round($rev, 2);
            $expenseValues[] = round($rev * 0.65, 2);
        }

        // 3. Weekly Sales Breakdown (Last 7 Days)
        $salesLabels = [];
        $salesValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $count = Order::where('status', '!=', 'cancelled')
                ->whereDate('created_at', $day->toDateString())
                ->count();

            $salesLabels[] = $day->format('D');
            $salesValues[] = $count;
        }

        // 4. Orders Trend (Last 7 Days)
        $orderTrendLabels = [];
        $orderTrendValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $count = Order::whereDate('created_at', $day->toDateString())->count();

            $orderTrendLabels[] = $day->format('M d');
            $orderTrendValues[] = $count;
        }

        // 5. Recent 5 Orders
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // 6. Top Selling Products
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as sales_count'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->with(['product.images'])
            ->get()
            ->filter(fn ($item) => $item->product !== null);

        return view('admin.dashboard.index', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'revenueLabels',
            'revenueValues',
            'expenseValues',
            'salesLabels',
            'salesValues',
            'orderTrendLabels',
            'orderTrendValues',
            'recentOrders',
            'topProducts'
        ));
    }
}
