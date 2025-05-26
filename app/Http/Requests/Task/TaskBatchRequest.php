<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.due_date' => 'nullable|date',
            'tasks.*.priority_id' => 'required|exists:task_priorities,id',
            'tasks.*.status_id' => 'required|exists:task_statuses,id',
            'tasks.*.category_ids' => 'nullable|array',
            'tasks.*.category_ids.*' => 'exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tasks.required' => 'The tasks field is required.',
            'tasks.*.title.required' => 'The title field is required for each task.',
            'tasks.*.priority_id.required' => 'The priority field is required for each task.',
            'tasks.*.status_id.required' => 'The status field is required for each task.',
        ];
    }
}
