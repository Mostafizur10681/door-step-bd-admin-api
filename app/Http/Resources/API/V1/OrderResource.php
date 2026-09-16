<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'user_id'            => $this->user_id,
            'user'               => new UserResource($this->whenLoaded('user')),
            'order_number'       => $this->order_number,
            'total'              => $this->total,
            'shipping_amount'    => $this->shipping_amount,
            'coupon_code'        => $this->coupon_code,
            'coupon_discount'    => $this->coupon_discount ? (float)$this->coupon_discount : 0,
            'status'             => $this->status,
            'payment_status'     => $this->payment_status,
            'payment_method'     => $this->payment_method ?? 'cod',
            'customer_name'      => $this->customer_name,
            'company_name'       => $this->company_name,
            'customer_phone'     => $this->customer_phone,
            'customer_email'     => $this->customer_email,
            'country'            => $this->country ?? 'Bangladesh',
            'division'           => $this->division,
            'district'           => $this->district,
            'thana'              => $this->thana,
            'address'            => $this->address,
            'town_city'          => $this->town_city,
            'postcode'           => $this->postcode,
            'order_notes'        => $this->order_notes,
            'ship_different'     => (bool)$this->ship_different,
            'ship_customer_name' => $this->ship_customer_name,
            'ship_company_name'  => $this->ship_company_name,
            'ship_country'       => $this->ship_country,
            'ship_address'       => $this->ship_address,
            'ship_town_city'     => $this->ship_town_city,
            'ship_postcode'      => $this->ship_postcode,
            'ship_district'      => $this->ship_district,
            'ship_phone'         => $this->ship_phone,
            'items'              => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}
