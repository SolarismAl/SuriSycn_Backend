<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'recurrence' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'department_id' => 'nullable|uuid|exists:departments,id',
            'tagged_users' => 'nullable|array',
            'tagged_users.*' => 'uuid|exists:users,id',
            'external_participants' => 'nullable|array',
            'external_participants.*' => 'email',
        ];
    }
}
