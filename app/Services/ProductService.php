<?php

namespace App\Services;

use App\Models\ProductImage;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Traits\UploadImageTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    use UploadImageTrait;

    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(array $relations = ['category']): Collection
    {
        return $this->productRepository->all(['*'], array_merge($relations, ['images', 'subCategory']));
    }

    public function paginateProducts(int $perPage = 15, array $relations = ['category'], array $filters = []): LengthAwarePaginator
    {
        $query = \App\Models\Product::with(array_merge($relations, ['images', 'subCategory']));

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        } elseif (!empty($filters['category'])) {
            $catSlugOrName = $filters['category'];
            $query->whereHas('category', function ($q) use ($catSlugOrName) {
                $q->where('slug', $catSlugOrName)->orWhere('name', $catSlugOrName)->orWhere('id', $catSlugOrName);
            });
        }

        if (!empty($filters['sub_category_id'])) {
            $query->where('sub_category_id', $filters['sub_category_id']);
        } elseif (!empty($filters['sub_category'])) {
            $subVal = $filters['sub_category'];
            $query->where(function ($q) use ($subVal) {
                $q->where('sub_category', $subVal)
                  ->orWhere('sub_category_id', $subVal)
                  ->orWhereHas('subCategory', function ($sq) use ($subVal) {
                      $sq->where('slug', $subVal)->orWhere('name', $subVal);
                  });
            });
        }

        if (isset($filters['featured'])) {
            $query->where('featured', filter_var($filters['featured'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['new_arrival'])) {
            $query->where('new_arrival', filter_var($filters['new_arrival'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['best_seller'])) {
            $query->where('best_seller', filter_var($filters['best_seller'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['organic'])) {
            $query->where('organic', filter_var($filters['organic'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['status'])) {
            $query->where('status', filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN));
        }

        // Sorting (default to latest new products first)
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'name_asc':
            case 'title_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
            case 'title_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_low_high':
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high_low':
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('id', 'asc');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        return $query->paginate($perPage);
    }

    public function getProductById(int|string $id, array $relations = ['category']): ?Model
    {
        if (!is_numeric($id)) {
            return \App\Models\Product::with(array_merge($relations, ['images']))->where('slug', $id)->firstOrFail();
        }
        return $this->productRepository->findOrFail($id, ['*'], array_merge($relations, ['images']));
    }

    public function createProduct(array $data): ?Model
    {
        $galleryPaths = [];

        // Handle single main image upload (if still using file upload)
        if (isset($data['image_file'])) {
            $galleryPaths[] = $this->uploadImage($data['image_file'], 'products');
            unset($data['image_file']);
        }

        // Handle Base64 images array from the request
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $base64) {
                // Store the raw Base64 string directly in product_images
                $galleryPaths[] = $this->uploadBase64Image($base64, 'products/gallery');
            }
            unset($data['images']);
        }

        // Keep legacy gallery_files handling for backward compatibility
        if (isset($data['gallery_files']) && is_array($data['gallery_files'])) {
            foreach ($data['gallery_files'] as $file) {
                $galleryPaths[] = $this->uploadImage($file, 'products/gallery');
            }
            unset($data['gallery_files']);
        }

        // Remove old image and gallery fields if present
        unset($data['image'], $data['gallery']);

        $product = $this->productRepository->create($data);
        if ($product && ! empty($galleryPaths)) {
            $this->storeImages($product->id, $galleryPaths);
        }

        return $product;
    }

    public function updateProduct(int|string $id, array $data): bool
    {
        $galleryPaths = [];

        // Handle single main image upload (if still using file upload)
        if (isset($data['image_file'])) {
            $galleryPaths[] = $this->uploadImage($data['image_file'], 'products');
            unset($data['image_file']);
        }

        // Handle Base64 images array from the request
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $base64) {
                // Store the raw Base64 string directly
                $galleryPaths[] = $this->uploadBase64Image($base64, 'products/gallery');
            }
            unset($data['images']);
        }

        // Keep legacy gallery_files handling for backward compatibility
        if (isset($data['gallery_files']) && is_array($data['gallery_files'])) {
            foreach ($data['gallery_files'] as $file) {
                $galleryPaths[] = $this->uploadImage($file, 'products/gallery');
            }
            unset($data['gallery_files']);
        }

        unset($data['image'], $data['gallery']);

        // Update product data
        $updated = $this->productRepository->update($id, $data);
        if ($updated) {
            // Remove old images
            ProductImage::where('product_id', $id)->delete();
            if (! empty($galleryPaths)) {
                $this->storeImages($id, $galleryPaths);
            }
        }

        return $updated;
    }

    /**
     * Store images for a product.
     */
    public function storeImages(int $productId, array $images): void
    {
        foreach ($images as $path) {
            ProductImage::create([
                'product_id' => $productId,
                'image_path' => $path,
            ]);
        }
    }
}
