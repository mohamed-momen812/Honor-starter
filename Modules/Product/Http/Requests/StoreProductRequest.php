<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-products');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['required', 'string', 'unique:products,sku'],
            'category_ids' => ['sometimes', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'description.string' => 'The product description must be a string.',
            'price.required' => 'The product price is required.',
            'price.numeric' => 'The product price must be a number.',
            'stock.required' => 'The product stock is required.',
            'stock.integer' => 'The product stock must be an integer.',
            'sku.required' => 'The SKU is required.',
            'sku.unique' => 'The SKU must be unique.',
            'category_ids.array' => 'The category IDs must be an array.',
            'category_ids.*.exists' => 'The selected category ID is invalid.',
            'category_ids.*.integer' => 'The category ID must be an integer.',
        ];
    }
}