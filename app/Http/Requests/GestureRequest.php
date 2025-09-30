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
            'gesture_data.frames.*.landmarks' => 'required|array',
            'gesture_data.frames.*.timestamp' => 'sometimes|string',
        ];

        // Para actualizaciones, hacer lesson_id opcional si ya existe
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
            'gesture_data.required' => 'Los datos del gesto son obligatorios.',
            'gesture_data.array' => 'Los datos del gesto deben ser un objeto JSON válido.',
            'gesture_data.name.required' => 'El nombre del gesto es obligatorio.',
            'gesture_data.name.string' => 'El nombre del gesto debe ser texto.',
            'gesture_data.name.max' => 'El nombre del gesto no puede exceder 255 caracteres.',
            'gesture_data.frames.required' => 'Los frames del gesto son obligatorios.',
            'gesture_data.frames.array' => 'Los frames deben ser un arreglo.',
            'gesture_data.frames.min' => 'Debe haber al menos un frame.',
            'gesture_data.frames.*.landmarks.required' => 'Los landmarks son obligatorios en cada frame.',
            'gesture_data.frames.*.landmarks.array' => 'Los landmarks deben ser un arreglo.',
        ];
    }
}
