<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'order_number',
        'total',
        'shipping_amount',
        'status',
        'payment_status',
        'payment_method',
        'customer_name',
        'company_name',
        'customer_phone',
        'customer_email',
        'country',
        'division',
        'district',
        'thana',
        'address',
        'town_city',
        'postcode',
        'order_notes',
        'ship_different',
        'ship_customer_name',
        'ship_company_name',
        'ship_country',
        'ship_address',
        'ship_town_city',
        'ship_postcode',
        'ship_district',
        'ship_phone',
        'coupon_code',
        'coupon_discount',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'coupon_discount' => 'decimal:2',
            'ship_different' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'order_number', 'total', 'status', 'payment_status', 'payment_method'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
