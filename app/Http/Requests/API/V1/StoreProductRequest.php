<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'SKU' => 'required|string|unique:products,SKU',
            'stock' => 'nullable|integer|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'gallery_files' => 'nullable|array',
            'gallery_files.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'status' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            // New: Accept base64 image strings array
            'images' => 'nullable|array',
            'images.*' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'image_file.max' => 'The product image must not be larger than 10MB.',
            'gallery_files.*.max' => 'Each gallery image must not be larger than 10MB.',
            'image_file.image' => 'The file must be a valid image.',
            'gallery_files.*.image' => 'Each gallery file must be a valid image.',
        ];
    }
}
