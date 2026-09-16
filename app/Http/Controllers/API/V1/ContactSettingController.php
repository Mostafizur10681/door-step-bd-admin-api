<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Http\Resources\API\V1\ContactSettingResource;
use App\Http\Resources\API\V1\WhatsAppSettingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContactSettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = ContactSetting::first();

        if (!$settings) {
            $settings = new ContactSetting();
        }

        return response()->json([
            'success' => true,
            'message' => 'Contact settings retrieved successfully',
            'data' => new ContactSettingResource($settings)
        ]);
    }

    public function whatsappIndex(): JsonResponse
    {
        $settings = ContactSetting::first();

        if (!$settings) {
            $settings = new ContactSetting();
        }

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp settings retrieved successfully',
            'data' => new WhatsAppSettingResource($settings)
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $settings = ContactSetting::first() ?: new ContactSetting();

        $data = $request->validate([
            'badge_text' => 'nullable|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'secondary_phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'secondary_email' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:255',
            'whatsapp_is_enabled' => 'nullable|boolean',
            'whatsapp_default_message' => 'nullable|string',
            'whatsapp_position' => 'nullable|string|in:bottom-right,bottom-left',
            'whatsapp_header_title' => 'nullable|string|max:255',
            'whatsapp_header_subtitle' => 'nullable|string|max:255',
            'whatsapp_button_text' => 'nullable|string|max:255',
            'whatsapp_show_floating_button' => 'nullable|boolean',
            'address' => 'nullable|string|max:255',
            'business_hours_weekday' => 'nullable|string|max:255',
            'business_hours_weekend' => 'nullable|string|max:255',
            'response_time_note' => 'nullable|string|max:255',
            'map_title' => 'nullable|string|max:255',
            'map_subtitle' => 'nullable|string',
            'map_url' => 'nullable|string',
            'location_directions' => 'nullable|string',
            'form_title' => 'nullable|string|max:255',
            'form_subtitle' => 'nullable|string',
            'form_topics' => 'nullable|array',
            'form_topics.*' => 'nullable|string',
            'emergency_notice' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*.icon' => 'nullable|string',
            'features.*.title' => 'nullable|string',
            'features.*.desc' => 'nullable|string',
            'support_title' => 'nullable|string|max:255',
            'support_desc' => 'nullable|string|max:255',
            'support_phone' => 'nullable|string|max:255',
            'support_image' => 'nullable|string',
        ]);

        // Process support image if it is a base64 string (store directly in database)
        if (isset($data['support_image']) && str_starts_with($data['support_image'], 'data:image')) {
            // Delete old file if it was a stored upload on disk
            if ($settings->support_image && str_contains($settings->support_image, 'storage/contact/')) {
                $oldPath = str_replace(url('storage') . '/', '', $settings->support_image);
                Storage::disk('public')->delete($oldPath);
            }
        }

        $settings->fill($data);
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => 'Contact settings updated successfully',
            'data' => new ContactSettingResource($settings)
        ]);
    }

    private function handleBase64Image(?string $base64String, string $folder): ?string
    {
        if (!$base64String) {
            return null;
        }

        // Check if it is a valid base64 image data URI
        if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
            $data = substr($base64String, strpos($base64String, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                return null;
            }

            $data = base64_decode($data);
            if ($data === false) {
                return null;
            }

            $fileName = Str::random(20) . '.' . $type;

            Storage::disk('public')->put($folder . '/' . $fileName, $data);

            return url('storage/' . $folder . '/' . $fileName);
        }

        return null;
    }
}
