<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait UploadImageTrait
{
    /**
     * Convert and optimize an uploaded image to a base64 Data URI string.
     */
    protected function uploadImage(UploadedFile $file, string $folder = 'products', int $width = 800, int $height = 800): string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            return '/hero_honey.png';
        }

        try {
            if (class_exists(ImageManager::class) && class_exists(Driver::class)) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($realPath);

                // Scale down nicely within max dimensions
                if (method_exists($image, 'scaleDown')) {
                    $image->scaleDown($width, $height);
                } elseif (method_exists($image, 'scale')) {
                    $image->scale($width, $height);
                }

                // Encode to lightweight WebP (75% quality) or JPEG (75% quality) Data URI
                if (method_exists($image, 'toWebp')) {
                    return (string) $image->toWebp(75)->toDataUri();
                } elseif (method_exists($image, 'toJpeg')) {
                    return (string) $image->toJpeg(75)->toDataUri();
                }
            }
        } catch (\Throwable $e) {
            // Fallback to direct raw base64 encode
        }

        // Direct raw Base64 Data URI fallback
        $mime = $file->getMimeType() ?: 'image/jpeg';
        $contents = @file_get_contents($realPath);
        $base64 = $contents ? base64_encode($contents) : '';

        return $base64 ? ('data:' . $mime . ';base64,' . $base64) : '/hero_honey.png';
    }

    /**
     * Validate, format, and optimize base64 image strings.
     */
    protected function uploadBase64Image(string $base64Data, string $folder = 'products', int $width = 800, int $height = 800): string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        if (!preg_match('/^data:image\/\w+;base64,/', $base64Data)) {
            $base64Data = 'data:image/jpeg;base64,' . $base64Data;
        }

        // If string is large (> 200KB), compress it using Intervention Image
        if (strlen($base64Data) > 200000) {
            try {
                if (class_exists(ImageManager::class) && class_exists(Driver::class)) {
                    $manager = new ImageManager(new Driver());
                    $rawBinary = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
                    if ($rawBinary) {
                        $image = $manager->read($rawBinary);

                        if (method_exists($image, 'scaleDown')) {
                            $image->scaleDown($width, $height);
                        } elseif (method_exists($image, 'scale')) {
                            $image->scale($width, $height);
                        }

                        if (method_exists($image, 'toWebp')) {
                            return (string) $image->toWebp(75)->toDataUri();
                        } elseif (method_exists($image, 'toJpeg')) {
                            return (string) $image->toJpeg(75)->toDataUri();
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Return original formatted data URI on failure
            }
        }

        return $base64Data;
    }

    /**
     * Delete image helper (safe for both file paths and base64 strings).
     */
    protected function deleteImage(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        // If it is a base64 data URI, nothing to delete on disk
        if (str_starts_with($path, 'data:image/')) {
            return true;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
