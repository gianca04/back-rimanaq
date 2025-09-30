<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Sin autenticación por ahora
    }

    /**
     * Configure the validator instance.
     */
    public function expectsJson(): bool
    {
        return true; // Fuerza respuestas JSON para APIs
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'user_id' => 'required|exists:users,id',
            'lesson_id' => 'required|exists:lessons,id',
            'course_id' => 'required|exists:courses,id',
            'completed' => 'sometimes|boolean',
            'attempts_count' => 'sometimes|integer|min:0',
        ];

        // Para actualizaciones, hacer los IDs opcionales
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['user_id'] = 'sometimes|exists:users,id';
            $rules['lesson_id'] = 'sometimes|exists:lessons,id';
            $rules['course_id'] = 'sometimes|exists:courses,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'lesson_id.required' => 'La lección es obligatoria.',
            'lesson_id.exists' => 'La lección seleccionada no existe.',
            'course_id.required' => 'El curso es obligatorio.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'completed.boolean' => 'El campo completado debe ser verdadero o falso.',
            'attempts_count.integer' => 'El número de intentos debe ser un entero.',
            'attempts_count.min' => 'El número de intentos no puede ser negativo.',
        ];
    }
}
