<?php

namespace Modules\Notification\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasPermissionTo('manage-notifications') ||
               ($this->notification->user_id === auth()->id() && $this->input('read_at'));
    }

    public function rules(): array
    {
        return [
            'read_at' => ['sometimes', 'date'],
        ];
    }
}
