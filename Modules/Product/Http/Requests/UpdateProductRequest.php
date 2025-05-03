<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-products');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'sku' => ['sometimes', 'string', Rule::unique('products')->ignore($this->product)],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The product name must be a string.',
            'description.string' => 'The product description must be a string.',
            'price.numeric' => 'The product price must be a number.',
            'stock.integer' => 'The product stock must be an integer.',
            'sku.string' => 'The SKU must be a string.',
            'sku.unique' => 'The SKU must be unique.',
            'category_ids.array' => 'The category IDs must be an array.',
            'category_ids.*.exists' => 'The selected category ID is invalid.',
            'category_ids.*.integer' => 'The category ID must be an integer.',
        ];
    }
}