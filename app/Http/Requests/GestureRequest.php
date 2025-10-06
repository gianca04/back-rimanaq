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
            'gesture_data' => 'required|array',
            'gesture_data.name' => 'required|string|max:255',
            'gesture_data.frames' => 'required|array|min:1',
            'gesture_data.frameCount' => 'required|integer|min:1',
            'gesture_data.isSequential' => 'required|boolean',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['lesson_id'] = 'sometimes|exists:lessons,id';
            $rules['gesture_data'] = 'sometimes|array';
            $rules['gesture_data.name'] = 'sometimes|string|max:255';
            $rules['gesture_data.frames'] = 'sometimes|array|min:1';
            $rules['gesture_data.frameCount'] = 'sometimes|integer|min:1';
            $rules['gesture_data.isSequential'] = 'sometimes|boolean';
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
            'gesture_data.required' => 'Los datos del gesto son obligatorios.',
            'gesture_data.name.required' => 'El nombre del gesto es obligatorio.',
            'gesture_data.frames.required' => 'Los frames del gesto son obligatorios.',
            'gesture_data.frames.min' => 'Debe tener al menos 1 frame.',
            'gesture_data.frameCount.required' => 'El número de frames es obligatorio.',
            'gesture_data.isSequential.required' => 'El tipo de gesto (secuencial) es obligatorio.',
        ];
    }
}
