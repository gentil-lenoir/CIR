<?php

namespace App\Http\Requests\Admin;

use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:departments,id'],
            'worker_id' => [
                'required',
                'exists:workers,id',
                Rule::exists('workers', 'id')->where(function ($query) {
                    return $query
                        ->where('department_id', $this->integer('department_id'))
                        ->where('status', Worker::STATUS_ACTIVE);
                }),
            ],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'status' => ['nullable', Rule::in(['reported', 'in_progress', 'resolved'])],
        ];
    }
}
