<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhatsAppSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array containing only WhatsApp connection settings.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) ($this->id ?? 1),
            'whatsapp_number' => $this->whatsapp_number ?? '8801800000000',
            'whatsapp_is_enabled' => (bool) ($this->whatsapp_is_enabled ?? true),
            'whatsapp_default_message' => $this->whatsapp_default_message ?? 'Hello! I have an inquiry regarding your products on Shopia.',
            'whatsapp_position' => $this->whatsapp_position ?? 'bottom-right',
            'whatsapp_header_title' => $this->whatsapp_header_title ?? 'Chat with WhatsApp Support',
            'whatsapp_header_subtitle' => $this->whatsapp_header_subtitle ?? 'Typically replies in a few minutes',
            'whatsapp_button_text' => $this->whatsapp_button_text ?? 'Start WhatsApp Chat',
            'whatsapp_show_floating_button' => (bool) ($this->whatsapp_show_floating_button ?? true),

            // Frontend helper aliases
            'default_message' => $this->whatsapp_default_message ?? 'Hello! I have an inquiry regarding your products on Shopia.',
            'enabled' => (bool) ($this->whatsapp_is_enabled ?? true),
            'position' => str_contains($this->whatsapp_position ?? '', 'left') ? 'left' : 'right',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
