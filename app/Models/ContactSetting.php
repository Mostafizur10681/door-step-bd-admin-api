<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use HasFactory;

    protected $table = 'contact_settings';

    protected $fillable = [
        'badge_text',
        'hero_title',
        'hero_subtitle',
        'phone',
        'secondary_phone',
        'email',
        'secondary_email',
        'whatsapp_number',
        'whatsapp_is_enabled',
        'whatsapp_default_message',
        'whatsapp_position',
        'whatsapp_header_title',
        'whatsapp_header_subtitle',
        'whatsapp_button_text',
        'whatsapp_show_floating_button',
        'address',
        'business_hours_weekday',
        'business_hours_weekend',
        'response_time_note',
        'map_title',
        'map_subtitle',
        'map_url',
        'location_directions',
        'form_title',
        'form_subtitle',
        'form_topics',
        'emergency_notice',
        'features',
        'support_title',
        'support_desc',
        'support_phone',
        'support_image',
    ];

    protected $casts = [
        'form_topics' => 'array',
        'features' => 'array',
        'whatsapp_is_enabled' => 'boolean',
        'whatsapp_show_floating_button' => 'boolean',
    ];
}
