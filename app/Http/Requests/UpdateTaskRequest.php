<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
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

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high',
            'progress' => 'nullable|integer|min:0|max:100',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|uuid|exists:users,id',
            'status' => 'nullable|string|in:pending,in_progress,in_review,completed',
        ];
    }
}
