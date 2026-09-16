<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $this->user()->id,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:1000',
            'gender' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
            'profile_pic' => 'nullable',
            'avatar' => 'nullable',
        ];
    }
}
