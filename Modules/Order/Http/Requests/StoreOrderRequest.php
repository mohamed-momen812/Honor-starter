<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-orders');
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'status' => ['sometimes', 'string', 'in:pending,confirmed,shipped,delivered'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'status.in' => 'The status must be one of the following: pending, confirmed, shipped, delivered.',
            'items.required' => 'At least one item is required in the order.',
            'items.array' => 'Items must be an array.',
            'items.min' => 'At least one item is required in the order.',
            'items.*.product_id.required' => 'The product ID is required for each item.',
            'items.*.product_id.exists' => 'The selected product does not exist.',
            'items.*.quantity.required' => 'The quantity is required for each item.',
            'items.*.quantity.integer' => 'The quantity must be an integer.',
            'items.*.quantity.min' => 'The quantity must be at least 1.',
        ];
    }
}