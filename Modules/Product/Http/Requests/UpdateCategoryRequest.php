<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-categories');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
        ];
    }
    public function messages(): array
    {
        return [
            'name.sometimes' => 'The category name is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
        ];
    }
}