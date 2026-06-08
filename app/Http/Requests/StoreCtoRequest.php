<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCtoRequest extends FormRequest
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
        $rules = [
            'type' => 'required|in:earned,used',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5',
            'reason' => 'required|string|max:500',
        ];

        if ($this->input('type') === 'earned') {
            $rules['date'] .= '|before_or_equal:today';
        }

        return $rules;
    }
}
