<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-users');
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($this->user)],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a string.',
            'email.email' => 'The email address must be a valid email format.',
            'email.unique' => 'The email address has already been taken.',
            'password.string' => 'The password must be a string.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'The password confirmation does not match.',
            'roles.array' => 'Roles must be an array.',
            'roles.*.exists' => 'One or more selected roles do not exist.',
        ];
    }
}