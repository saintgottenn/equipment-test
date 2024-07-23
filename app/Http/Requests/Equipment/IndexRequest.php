<?php

namespace App\Http\Requests\Equipment;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
            'equipment_type_id' => ['nullable', 'integer', 'exists:equipment_types,id'],
            'serial_number' => ['nullable', 'string'],
            'desc' => ['nullable', 'string'],
            'q' => ['nullable', 'string'],
        ];
    }
}
