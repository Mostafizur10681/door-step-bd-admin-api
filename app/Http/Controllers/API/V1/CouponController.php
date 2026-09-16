<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate and apply a coupon code to a given cart subtotal.
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
            'user_id' => 'nullable|integer',
        ]);

        $code = strtoupper(trim($request->input('code')));
        $subtotal = (float)$request->input('subtotal', 0);

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => "Coupon code '{$code}' is invalid.",
            ], 404);
        }

        if (!$coupon->is_active) {
            return response()->json([
                'success' => false,
                'message' => "This coupon is currently inactive.",
            ], 422);
        }

        $now = \Carbon\Carbon::now('Asia/Dhaka');

        // Check if schedule starts in the future (Bangladesh Time)
        if ($coupon->starts_at) {
            $startsAt = \Carbon\Carbon::parse($coupon->starts_at, 'Asia/Dhaka');
            if ($now->lt($startsAt)) {
                return response()->json([
                    'success' => false,
                    'message' => "This coupon promotion will be available on " . $startsAt->format('M d, Y \a\t h:i A') . ".",
                ], 422);
            }
        }

        // Check if expired (Bangladesh Time)
        if ($coupon->expires_at) {
            $expiresAt = \Carbon\Carbon::parse($coupon->expires_at, 'Asia/Dhaka');
            if ($now->gt($expiresAt)) {
                return response()->json([
                    'success' => false,
                    'message' => "This coupon expired on " . $expiresAt->format('M d, Y \a\t h:i A') . ".",
                ], 422);
            }
        }

        // Check total usage limit
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => "This coupon has reached its maximum total redemptions limit.",
            ], 422);
        }

        // Check user usage limit
        $userId = $request->user()?->id ?? $request->input('user_id');
        if ($userId && $coupon->hasUserExceededLimit((int)$userId)) {
            return response()->json([
                'success' => false,
                'message' => "You have already reached the maximum usage limit for this coupon.",
            ], 422);
        }

        // Check minimum order amount
        if ($coupon->minimum_order_amount && $subtotal < $coupon->minimum_order_amount) {
            $diff = $coupon->minimum_order_amount - $subtotal;
            return response()->json([
                'success' => false,
                'message' => "Minimum subtotal of ৳" . number_format($coupon->minimum_order_amount, 2) . " required. Add ৳" . number_format($diff, 2) . " more to use this coupon.",
            ], 422);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($subtotal);
        $newSubtotal = max(0, $subtotal - $discount);

        return response()->json([
            'success' => true,
            'message' => "Coupon '{$coupon->code}' applied! You saved ৳" . number_format($discount, 2) . ".",
            'data' => [
                'id'                   => $coupon->id,
                'code'                 => $coupon->code,
                'name'                 => $coupon->name,
                'description'          => $coupon->description,
                'discount_type'        => $coupon->discount_type,
                'discount_value'       => (float)$coupon->discount_value,
                'discount_amount'      => round($discount, 2),
                'formatted_discount'   => '৳' . number_format($discount, 2),
                'discount_label'       => $coupon->discount_label,
                'subtotal'             => round($subtotal, 2),
                'new_subtotal'         => round($newSubtotal, 2),
                'minimum_order_amount' => $coupon->minimum_order_amount ? (float)$coupon->minimum_order_amount : null,
                'maximum_discount'     => $coupon->maximum_discount ? (float)$coupon->maximum_discount : null,
                'starts_at'            => $coupon->starts_at ? $coupon->starts_at->toIso8601String() : null,
                'expires_at'           => $coupon->expires_at ? $coupon->expires_at->toIso8601String() : null,
            ]
        ]);
    }
}
