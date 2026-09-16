<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_line1',
        'title_line2',
        'subtitle',
        'image',
        'mobile_image',
        'left_image',
        'bg_color',
        'right_bg_color',
        'badge',
        'cta_text',
        'cta_link',
        'order',
        'is_active',
        'menu_location',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
}
