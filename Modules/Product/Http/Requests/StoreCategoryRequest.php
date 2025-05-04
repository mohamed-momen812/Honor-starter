<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-categories');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($this->category)],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ];
    }


    public function messages(): array
    {
        return [
            'name.unique' => 'The category name has already been taken.',
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'category_id.exists' => 'The selected category does not exist.',
            'category_id.integer' => 'The category ID must be an integer.',
        ];
    }
}