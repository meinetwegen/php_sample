<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $max = config('shop.max_cart_quantity');

        return [
            'magazine_id' => ['required', 'integer', 'exists:magazines,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', "max:{$max}"],
        ];
    }
}
