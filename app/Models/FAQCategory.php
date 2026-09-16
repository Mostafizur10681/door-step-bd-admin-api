<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FAQCategory extends Model
{
    use HasFactory, Sluggable, LogsActivity;

    protected $table = 'faq_categories';

    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
    ];

    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function faqs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Faq::class, 'category', 'name');
    }

    public function getStatusAttribute($value)
    {
        if (is_bool($value) || $value === 1 || $value === 0 || $value === '1' || $value === '0') {
            return $value ? 'active' : 'inactive';
        }
        return $value;
    }
}
