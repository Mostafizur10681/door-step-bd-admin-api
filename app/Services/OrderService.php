<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAllOrders(array $relations = ['user', 'items.product']): Collection
    {
        return $this->orderRepository->all(['*'], $relations);
    }

    public function paginateOrders(int $perPage = 15, array $relations = ['user', 'items.product']): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($perPage, $relations);
    }

    public function getOrderById(int|string $id, array $relations = ['user', 'items.product.images']): ?Model
    {
        if (is_numeric($id)) {
            $order = $this->orderRepository->find($id, ['*'], $relations);
            if ($order) {
                return $order;
            }
        }

        return Order::with($relations)->where('order_number', $id)->firstOrFail();
    }

    public function createOrder(array $data): ?Model
    {
        return DB::transaction(function () use ($data) {
            // Generate unique order number
            $data['order_number'] = 'ORD-'.date('Ymd').'-'.strtoupper(Str::random(6));

            // Calculate total price from items
            $total = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Use sale price if active, otherwise normal price
                $price = $product->sale_price ?? $product->price;
                $subtotal = $price * $item['quantity'];
                $total += $subtotal;

                // Adjust product stock
                if ($product->stock !== null && $product->stock < $item['quantity']) {
                    throw new \Exception("Product {$product->name} does not have enough stock.");
                }
                if ($product->stock !== null) {
                    $product->decrement('stock', $item['quantity']);
                }

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'attributes' => $item['attributes'] ?? null,
                ];
            }

            // Calculate coupon discount
            $subtotalItems = $total;
            $couponCode = !empty($data['coupon_code']) ? strtoupper(trim($data['coupon_code'])) : null;
            $couponDiscount = 0;
            $couponModel = null;

            if ($couponCode) {
                $couponModel = \App\Models\Coupon::where('code', $couponCode)->first();
                if ($couponModel && $couponModel->isValid()) {
                    $couponDiscount = $couponModel->calculateDiscount($subtotalItems);
                }
            }

            if ($couponDiscount <= 0 && !empty($data['coupon_discount'])) {
                $couponDiscount = min((float)$data['coupon_discount'], $subtotalItems);
            }

            // Add shipping cost: Dhaka = 60 BDT (or flat), Outside Dhaka = 120 BDT
            $targetDistrict = (!empty($data['ship_different']) && !empty($data['ship_district'])) ? $data['ship_district'] : ($data['district'] ?? 'Dhaka');
            $shippingCost = isset($data['shipping_amount']) ? (float)$data['shipping_amount'] : (str_contains(strtolower($targetDistrict), 'dhaka') ? 60 : 120);
            
            // Grand Total
            $grandTotal = max(0, ($subtotalItems - $couponDiscount) + $shippingCost);
            $data['total'] = $grandTotal;

            // Create order with all billing, shipping and coupon details
            $order = $this->orderRepository->create([
                'user_id'            => $data['user_id'] ?? null,
                'order_number'       => $data['order_number'],
                'total'              => $data['total'],
                'shipping_amount'    => $shippingCost,
                'coupon_code'        => $couponCode,
                'coupon_discount'    => $couponDiscount,
                'status'             => $data['status'] ?? 'pending',
                'payment_status'     => $data['payment_status'] ?? 'pending',
                'payment_method'     => $data['payment_method'] ?? 'cod',
                'customer_name'      => $data['customer_name'],
                'company_name'       => $data['company_name'] ?? null,
                'customer_phone'     => $data['customer_phone'],
                'customer_email'     => $data['customer_email'] ?? null,
                'country'            => $data['country'] ?? 'Bangladesh',
                'division'           => $data['division'] ?? ($data['district'] ?? 'Dhaka'),
                'district'           => $data['district'] ?? 'Dhaka',
                'thana'              => $data['thana'] ?? ($data['town_city'] ?? 'Dhaka Sadar'),
                'address'            => $data['address'],
                'town_city'          => $data['town_city'] ?? null,
                'postcode'           => $data['postcode'] ?? null,
                'order_notes'        => $data['order_notes'] ?? null,
                'ship_different'     => !empty($data['ship_different']),
                'ship_customer_name' => $data['ship_customer_name'] ?? null,
                'ship_company_name'  => $data['ship_company_name'] ?? null,
                'ship_country'       => $data['ship_country'] ?? null,
                'ship_address'       => $data['ship_address'] ?? null,
                'ship_town_city'     => $data['ship_town_city'] ?? null,
                'ship_postcode'      => $data['ship_postcode'] ?? null,
                'ship_district'      => $data['ship_district'] ?? null,
                'ship_phone'         => $data['ship_phone'] ?? null,
            ]);

            // Save order items
            $order->items()->createMany($itemsData);

            // Record coupon usage
            if ($couponModel && $couponDiscount > 0) {
                $couponModel->increment('used_count');
                try {
                    \App\Models\CouponUsage::create([
                        'coupon_id'       => $couponModel->id,
                        'order_id'        => $order->id,
                        'user_id'         => $order->user_id,
                        'discount_amount' => $couponDiscount,
                    ]);
                } catch (\Exception $e) {}
            }

            return $order;
        });
    }

    public function updateOrderStatus(int|string $id, string $status, ?string $paymentStatus = null): bool
    {
        $payload = ['status' => $status];
        if ($paymentStatus !== null) {
            $payload['payment_status'] = $paymentStatus;
        }

        return $this->orderRepository->update($id, $payload);
    }

    public function deleteOrder(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $order = $this->orderRepository->find($id);
            if ($order) {
                // Restore product stock when order is deleted / cancelled
                if ($order->status !== 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->product) {
                            $item->product->increment('stock', $item->quantity);
                        }
                    }
                }

                return $order->delete();
            }

            return false;
        });
    }
}
