<?php

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-orders');
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'in:pending,confirmed,shipped,delivered'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'The status must be one of the following: pending, confirmed, shipped, delivered.',
        ];
    }
}