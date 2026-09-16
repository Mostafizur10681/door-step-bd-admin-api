<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if the coupon is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = \Carbon\Carbon::now('Asia/Dhaka');

        if ($this->starts_at) {
            $startsAt = \Carbon\Carbon::parse($this->starts_at, 'Asia/Dhaka');
            if ($now->lt($startsAt)) {
                return false;
            }
        }

        if ($this->expires_at) {
            $expiresAt = \Carbon\Carbon::parse($this->expires_at, 'Asia/Dhaka');
            if ($now->gt($expiresAt)) {
                return false;
            }
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if a specific user has exceeded their per-user usage limit.
     */
    public function hasUserExceededLimit(?int $userId): bool
    {
        if ($this->usage_limit_per_user === null || $userId === null) {
            return false;
        }

        $userUsageCount = $this->usages()->where('user_id', $userId)->count();
        return $userUsageCount >= $this->usage_limit_per_user;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->minimum_order_amount && $subtotal < $this->minimum_order_amount) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;

            if ($this->maximum_discount !== null && $discount > $this->maximum_discount) {
                $discount = (float) $this->maximum_discount;
            }
        } else {
            $discount = (float) $this->discount_value;
        }

        // Don't exceed the subtotal
        return min($discount, $subtotal);
    }

    /**
     * Get the display label (e.g. "10%" or "৳50 OFF").
     */
    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '% OFF';
        }
        return '৳' . number_format($this->discount_value, 0) . ' OFF';
    }

    /**
     * Scope: Only active and date-valid coupons.
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            });
    }
}
