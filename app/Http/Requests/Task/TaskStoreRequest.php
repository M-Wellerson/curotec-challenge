<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'priority_id' => ['required', 'exists:task_priorities,id'],
            'status_id' => ['required', 'exists:task_statuses,id'],
            'due_date' => ['nullable', 'date'],
            'category_ids.*' => 'exists:categories,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'title.max' => 'The title field must not exceed 255 characters.',
            'priority_id.required' => 'The priority field is required.',
            'priority_id.exists' => 'The selected priority does not exist.',
            'status_id.required' => 'The status field is required.',
            'status_id.exists' => 'The selected status does not exist.',
            'due_date.date' => 'The due date field must be a valid date.',
            'category_ids.*.exists' => 'The selected category does not exist.',
        ];
    }
}
