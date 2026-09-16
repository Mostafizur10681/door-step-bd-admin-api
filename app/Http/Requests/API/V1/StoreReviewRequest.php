<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;


class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $mergeData = [];

        // Product ID alias mapping
        if (!$this->has('product_id')) {
            if ($this->has('productId')) {
                $mergeData['product_id'] = $this->input('productId');
            } elseif ($this->has('item_id')) {
                $mergeData['product_id'] = $this->input('item_id');
            }
        }

        // Author Name alias mapping
        if (!$this->has('author_name')) {
            if ($this->has('name')) {
                $mergeData['author_name'] = $this->input('name');
            } elseif ($this->has('user_name')) {
                $mergeData['author_name'] = $this->input('user_name');
            } elseif ($this->has('username')) {
                $mergeData['author_name'] = $this->input('username');
            } elseif ($this->has('author')) {
                $mergeData['author_name'] = $this->input('author');
            }
        }

        // Comment alias mapping
        if (!$this->has('comment')) {
            if ($this->has('review')) {
                $mergeData['comment'] = $this->input('review');
            } elseif ($this->has('message')) {
                $mergeData['comment'] = $this->input('message');
            } elseif ($this->has('content')) {
                $mergeData['comment'] = $this->input('content');
            } elseif ($this->has('feedback')) {
                $mergeData['comment'] = $this->input('feedback');
            }
        }

        // Rating alias mapping
        if (!$this->has('rating')) {
            if ($this->has('stars')) {
                $mergeData['rating'] = $this->input('stars');
            } elseif ($this->has('rate')) {
                $mergeData['rating'] = $this->input('rate');
            } elseif ($this->has('score')) {
                $mergeData['rating'] = $this->input('score');
            }
        }

        // Logged-in User Data
        $user = $this->user() ?? auth('sanctum')->user();
        if ($user) {
            $mergeData['user_id'] = $user->id;
            if (empty($this->input('author_name')) && empty($mergeData['author_name'])) {
                $mergeData['author_name'] = $user->name;
            }
        }

        // Default Status
        if (!$this->has('status')) {
            $mergeData['status'] = 'approved';
        }

        if (!empty($mergeData)) {
            $this->merge($mergeData);
        }
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'nullable',
            'author_name' => 'nullable|string|max:255',
            'author_designation' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'status' => 'nullable|string',
            'image_path' => 'nullable|string',
        ];
    }
}
