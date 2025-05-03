<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-categories');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
        ];
    }
}