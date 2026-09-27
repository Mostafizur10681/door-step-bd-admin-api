<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'category_tag',
        'badge',
        'sub_title',
        'description',
        'capacity_range',
        'warranty_text',
        'key_capabilities',
        'cta_text',
        'cta_link',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'key_capabilities' => 'array',
    ];
}
