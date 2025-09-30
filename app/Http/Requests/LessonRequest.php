<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
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
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'level_number' => 'required|integer|min:1',
            'description' => 'required|string',
            'difficulty' => 'required|in:fácil,facil,intermedio,difícil,dificil',
            'time_minutes' => 'required|integer|min:1|max:600', // máximo 10 horas
        ];

        // Para actualizaciones, hacer campos opcionales
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['course_id'] = 'sometimes|exists:courses,id';
            $rules['name'] = 'sometimes|string|max:255';
            $rules['level_number'] = 'sometimes|integer|min:1';
            $rules['description'] = 'sometimes|string';
            $rules['difficulty'] = 'sometimes|in:fácil,facil,intermedio,difícil,dificil';
            $rules['time_minutes'] = 'sometimes|integer|min:1|max:600';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'El curso es obligatorio.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            'name.required' => 'El nombre de la lección es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'level_number.required' => 'El número de nivel es obligatorio.',
            'level_number.integer' => 'El número de nivel debe ser un entero.',
            'level_number.min' => 'El número de nivel debe ser al menos 1.',
            'description.required' => 'La descripción es obligatoria.',
            'difficulty.required' => 'La dificultad es obligatoria.',
            'difficulty.in' => 'La dificultad debe ser: fácil, intermedio o difícil.',
            'time_minutes.required' => 'El tiempo en minutos es obligatorio.',
            'time_minutes.integer' => 'El tiempo debe ser un número entero.',
            'time_minutes.min' => 'El tiempo debe ser al menos 1 minuto.',
            'time_minutes.max' => 'El tiempo no puede exceder los 600 minutos (10 horas).',
        ];
    }
}
