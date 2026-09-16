<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $imageUrls = [];
        if ($this->relationLoaded('images')) {
            foreach ($this->images as $img) {
                $path = trim($img->image_path);
                // If stored as a Base64 data URI, return it directly; otherwise build a storage URL.
                if (Str::contains($path, 'data:image')) {
                    // Extract the data URI part if the path includes a storage URL prefix
                    $base64Pos = strpos($path, 'data:image');
                    $imageUrls[] = $base64Pos !== false ? substr($path, $base64Pos) : $path;
                } else {
                    $imageUrls[] = url(Storage::url($path));
                }
            }
        }

        // Determine main image: if the product has a dedicated image column use it; otherwise fallback to first gallery image.
        $mainImage = $this->image;
        if (empty($mainImage) && count($imageUrls) > 0) {
            $mainImage = $imageUrls[0];
        }

        $price = (float) ($this->price ?? 0);
        $salePrice = ($this->sale_price && (float)$this->sale_price > 0 && (float)$this->sale_price < $price) ? (float) $this->sale_price : null;
        $discountVal = ($this->discount && (float)$this->discount > 0) ? (float) $this->discount : null;

        $discountPercentage = null;
        if ($price > 0 && $salePrice !== null && $salePrice < $price) {
            $discountPercentage = (int) round((($price - $salePrice) / $price) * 100);
        } elseif ($discountVal !== null && $discountVal > 0) {
            $discountPercentage = (int) round($discountVal);
        }
        if ($discountPercentage !== null && $discountPercentage <= 0) {
            $discountPercentage = null;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $price,
            'sale_price' => $salePrice,
            'discount' => $discountVal,
            'discount_percentage' => $discountPercentage,
            'on_sale' => $salePrice !== null,
            'SKU' => $this->SKU ?? $this->sku,
            'sku' => $this->sku ?? $this->SKU,
            'brand' => $this->brand,
            'color' => $this->color,
            'unit' => $this->unit,
            'stock' => $this->stock,
            'stock_status' => $this->stock_status,
            'featured' => (bool) $this->featured,
            'best_seller' => (bool) $this->best_seller,
            'organic' => (bool) $this->organic,
            'new_arrival' => (bool) $this->new_arrival,
            // Return the base64 data URI directly if it is stored as such; otherwise build a storage URL.
            // Ensure main image is a clean data URI if applicable
            'image' => $mainImage ? (function ($img) {
                $img = trim($img);
                return Str::contains($img, 'data:image') ? (strpos($img, 'data:image') !== false ? substr($img, strpos($img, 'data:image')) : $img) : url(Storage::url($img));
            })($mainImage) : null,
            'images' => $imageUrls,
            'status' => (bool) $this->status,
            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'sub_category' => $this->sub_category,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'attributes' => $this->attributes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
