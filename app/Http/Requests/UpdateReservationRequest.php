<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We'll handle authorization in the controller/middleware
    }

    public function rules(): array
    {
        return [
            'status'     => 'sometimes|string|in:approved,rejected,pending',
            'room_name'  => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date',
            'end_time'   => 'sometimes|date|after:start_time',
        ];
    }
}
