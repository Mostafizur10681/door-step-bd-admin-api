<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $user = $this->user();
        if (!$user && $token = $this->bearerToken()) {
            $model = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($model && $model->tokenable instanceof \App\Models\User) {
                $user = $model->tokenable;
            }
        }

        if ($user && !$this->has('user_id')) {
            $this->merge([
                'user_id' => $user->id,
            ]);
        }

        // Set sensible defaults for division, thana, country, and payment_method if omitted
        $district = $this->input('district', 'Dhaka');
        $townCity = $this->input('town_city', 'Dhaka');

        $this->merge([
            'division'       => $this->input('division') ?: $district,
            'thana'          => $this->input('thana') ?: $townCity,
            'country'        => $this->input('country', 'Bangladesh'),
            'payment_method' => $this->input('payment_method', 'cod'),
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id'            => 'nullable|exists:users,id',
            'shipping_amount'    => 'nullable|numeric|min:0',
            'customer_name'      => 'required|string|max:255',
            'company_name'       => 'nullable|string|max:255',
            'customer_phone'     => 'required|string|max:30',
            'customer_email'     => 'nullable|email|max:255',
            'country'            => 'nullable|string|max:100',
            'division'           => 'nullable|string|max:255',
            'district'           => 'required|string|max:255',
            'thana'              => 'nullable|string|max:255',
            'address'            => 'required|string|max:1000',
            'town_city'          => 'nullable|string|max:255',
            'postcode'           => 'nullable|string|max:50',
            'order_notes'        => 'nullable|string|max:2000',
            'payment_method'     => 'nullable|string|max:50',
            'ship_different'     => 'nullable|boolean',
            'ship_customer_name' => 'nullable|string|max:255',
            'ship_company_name'  => 'nullable|string|max:255',
            'ship_country'       => 'nullable|string|max:100',
            'ship_address'       => 'nullable|string|max:1000',
            'ship_town_city'     => 'nullable|string|max:255',
            'ship_postcode'      => 'nullable|string|max:50',
            'ship_district'      => 'nullable|string|max:255',
            'ship_phone'         => 'nullable|string|max:30',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.attributes' => 'nullable|array',
            'coupon_code'        => 'nullable|string|max:50',
            'coupon_discount'    => 'nullable|numeric|min:0',
        ];
    }
}
