<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $desktopImg = $this->image ? (str_starts_with($this->image, 'data:') || str_starts_with($this->image, 'http') || str_starts_with($this->image, '/') ? $this->image : asset('storage/' . $this->image)) : null;
        $mobileImg = $this->mobile_image ? (str_starts_with($this->mobile_image, 'data:') || str_starts_with($this->mobile_image, 'http') || str_starts_with($this->mobile_image, '/') ? $this->mobile_image : asset('storage/' . $this->mobile_image)) : $desktopImg;
        $leftImg = $this->left_image ? (str_starts_with($this->left_image, 'data:') || str_starts_with($this->left_image, 'http') || str_starts_with($this->left_image, '/') ? $this->left_image : asset('storage/' . $this->left_image)) : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'title_line1' => $this->title_line1,
            'title_line2' => $this->title_line2,
            'titleLine1' => $this->title_line1,
            'titleLine2' => $this->title_line2,
            'subtitle' => $this->subtitle,
            'discount_text' => $this->subtitle,
            'discountText' => $this->subtitle,
            'image' => $desktopImg,
            'desktop_image' => $desktopImg,
            'desktopImage' => $desktopImg,
            'mobile_image' => $mobileImg,
            'mobileImage' => $mobileImg,
            'left_image' => $leftImg,
            'bg_color' => $this->bg_color ?: '#002B49',
            'bgColor' => $this->bg_color ?: '#002B49',
            'right_bg_color' => $this->right_bg_color ?: ($this->bg_color ?: '#002B49'),
            'badge' => $this->badge,
            'tagline' => $this->badge,
            'cta_text' => $this->cta_text,
            'ctaText' => $this->cta_text,
            'cta_link' => $this->cta_link ?: '/',
            'ctaLink' => $this->cta_link ?: '/',
            'link' => $this->cta_link ?: '/',
            'order' => (int) ($this->order ?? 0),
            'is_active' => (bool) ($this->is_active ?? true),
            'menu_location' => $this->menu_location ?: 'home_hero_slider',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
