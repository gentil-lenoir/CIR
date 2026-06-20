<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('workers', 'email')],
            'phone' => ['nullable', 'string', 'max:25'],
            'department_id' => ['required', 'exists:departments,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'availability_status' => ['required', Rule::in(['available', 'busy', 'offline'])],
            'theme_preference' => ['required', Rule::in(['light', 'dark'])],
            'preferred_zone' => ['nullable', 'string', 'max:255'],
            'shift_window' => ['nullable', 'string', 'max:255'],
        ];
    }
}
