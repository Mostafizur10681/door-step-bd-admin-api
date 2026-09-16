<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentStatus;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhere('customer_email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product.images'])->findOrFail($id);
        $orderStatuses = OrderStatus::all();
        $paymentStatuses = PaymentStatus::all();

        return view('admin.orders.show', compact('order', 'orderStatuses', 'paymentStatuses'));
    }

    public function invoice($id)
    {
        $order = Order::with(['user', 'items.product.images'])->findOrFail($id);
        $contact = \App\Models\ContactSetting::first();
        $footer = \App\Models\FooterSetting::first();

        return view('admin.orders.invoice', compact('order', 'contact', 'footer'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->validate([
            'status' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        if (isset($data['status'])) {
            $order->status = $data['status'];
        }

        if (isset($data['payment_status'])) {
            $order->payment_status = $data['payment_status'];
        }

        $order->save();

        return back()->with('success', 'Order status updated successfully!');
    }

    public function paymentStatus()
    {
        $statuses = PaymentStatus::latest()->paginate(15);
        return view('admin.orders.payment-status', compact('statuses'));
    }

    public function storePaymentStatus(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:payment_statuses,name',
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|in:active,inactive',
        ]);

        PaymentStatus::create($data);

        return redirect()->route('admin.orders.payment-status')->with('success', 'Payment status created successfully!');
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $status = PaymentStatus::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:payment_statuses,name,' . $status->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|in:active,inactive',
        ]);

        $status->update($data);

        return redirect()->route('admin.orders.payment-status')->with('success', 'Payment status updated successfully!');
    }

    public function destroyPaymentStatus($id)
    {
        $status = PaymentStatus::findOrFail($id);
        $status->delete();

        return redirect()->route('admin.orders.payment-status')->with('success', 'Payment status deleted successfully!');
    }

    public function orderStatus()
    {
        $statuses = OrderStatus::latest()->paginate(15);
        return view('admin.orders.order-status', compact('statuses'));
    }

    public function storeOrderStatus(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:order_statuses,name',
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|in:active,inactive',
        ]);

        OrderStatus::create($data);

        return redirect()->route('admin.orders.order-status')->with('success', 'Order status created successfully!');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $status = OrderStatus::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:order_statuses,name,' . $status->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|in:active,inactive',
        ]);

        $status->update($data);

        return redirect()->route('admin.orders.order-status')->with('success', 'Order status updated successfully!');
    }

    public function destroyOrderStatus($id)
    {
        $status = OrderStatus::findOrFail($id);
        $status->delete();

        return redirect()->route('admin.orders.order-status')->with('success', 'Order status deleted successfully!');
    }
}
