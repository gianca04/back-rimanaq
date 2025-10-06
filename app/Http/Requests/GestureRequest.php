<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GestureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Sin autenticación por ahora
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'lesson_id' => 'required|exists:lessons,id',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['lesson_id'] = 'sometimes|exists:lessons,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lesson_id.required' => 'La lección es obligatoria.',
            'lesson_id.exists' => 'La lección seleccionada no existe.',
        ];
    }
}
