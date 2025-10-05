<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

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
            'course_id' => 'required|integer|exists:courses,id',
            'name' => 'required|string|min:3|max:255',
            'level_number' => 'required|integer|min:1|max:100',
            'description' => 'required|string|min:10|max:2000',
            'difficulty' => 'required|in:fácil,facil,intermedio,difícil,dificil',
            'time_minutes' => 'required|integer|min:5|max:600', // mínimo 5 min, máximo 10 horas
            'content' => 'nullable', // Sin validación específica, acepta cualquier tipo
        ];

        // Para actualizaciones, hacer campos opcionales pero mantener validaciones
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'course_id' => 'sometimes|integer|exists:courses,id',
                'name' => 'sometimes|string|min:3|max:255',
                'level_number' => 'sometimes|integer|min:1|max:100',
                'description' => 'sometimes|string|min:10|max:2000',
                'difficulty' => 'sometimes|in:fácil,facil,intermedio,difícil,dificil',
                'time_minutes' => 'sometimes|integer|min:5|max:600',
                'content' => 'sometimes|nullable', // Sin validación específica
            ];
        }

        return $rules;
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalizar la dificultad para manejar acentos
        if ($this->has('difficulty')) {
            $difficulty = strtolower(trim($this->difficulty));
            $difficultyMap = [
                'facil' => 'fácil',
                'fácil' => 'fácil',
                'intermedio' => 'intermedio',
                'dificil' => 'difícil',
                'difícil' => 'difícil'
            ];
            
            $this->merge([
                'difficulty' => $difficultyMap[$difficulty] ?? $difficulty,
                'name' => $this->name ? trim($this->name) : $this->name,
                'description' => $this->description ? trim($this->description) : $this->description,
            ]);
        }

        // El campo content se acepta tal como viene (string, array, JSON, etc.)
        // No se aplica ninguna transformación o validación específica
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'course_id.required' => 'El curso es obligatorio.',
            'course_id.integer' => 'El ID del curso debe ser un número entero.',
            'course_id.exists' => 'El curso seleccionado no existe.',
            
            'name.required' => 'El nombre de la lección es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            
            'level_number.required' => 'El número de nivel es obligatorio.',
            'level_number.integer' => 'El número de nivel debe ser un entero.',
            'level_number.min' => 'El número de nivel debe ser al menos 1.',
            'level_number.max' => 'El número de nivel no puede ser mayor a 100.',
            
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser un texto válido.',
            'description.min' => 'La descripción debe tener al menos 10 caracteres.',
            'description.max' => 'La descripción no puede exceder los 2000 caracteres.',
            
            'difficulty.required' => 'La dificultad es obligatoria.',
            'difficulty.in' => 'La dificultad debe ser: fácil, intermedio o difícil.',
            
            'time_minutes.required' => 'El tiempo en minutos es obligatorio.',
            'time_minutes.integer' => 'El tiempo debe ser un número entero.',
            'time_minutes.min' => 'El tiempo debe ser al menos 5 minutos.',
            'time_minutes.max' => 'El tiempo no puede exceder los 600 minutos (10 horas).',
        ];
    }
    
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Los datos enviados no son válidos.',
                'errors' => $validator->errors(),
                'meta' => [
                    'validation_failed' => true,
                    'error_count' => $validator->errors()->count()
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
