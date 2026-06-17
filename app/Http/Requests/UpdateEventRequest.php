<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'recurrence' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'department_id' => 'nullable|uuid|exists:departments,id',
            'tagged_users' => 'nullable|array',
            'tagged_users.*' => 'uuid|exists:users,id',
            'external_participants' => 'nullable|array',
            'external_participants.*' => 'email',
            'is_meeting' => 'nullable|boolean',
            'is_reminder' => 'nullable|boolean',
        ];
    }
}
