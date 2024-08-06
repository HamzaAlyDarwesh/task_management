<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|min:2|max:100',
            'description' => 'sometimes|required|string|min:10|max:500',
            'status_id' => 'sometimes|required|exists:task_statuses,id',
            'due_date' => 'sometimes|required|date|date_format:Y-m-d',
        ];
    }
}
