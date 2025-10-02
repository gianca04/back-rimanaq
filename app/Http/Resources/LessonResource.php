<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'level_number' => $this->level_number,
            'difficulty' => $this->difficulty,
            'difficulty_label' => $this->difficulty_label,
            'time_minutes' => $this->time_minutes,
            'formatted_duration' => $this->formatted_duration,
            'level_display' => $this->level_display,
            'difficulty_color' => $this->difficulty_color,
            
            // Relaciones
            'course' => $this->whenLoaded('course', function () {
                return [
                    'id' => $this->course->id,
                    'name' => $this->course->name,
                    'description' => $this->course->description
                ];
            }),
            
            // Contadores relacionados
            'gestures_count' => $this->whenCounted('gestures'),
            'progress_count' => $this->whenCounted('progress'),
            
            // Estados y banderas
            'has_gestures' => $this->when(
                $this->relationLoaded('gestures'),
                fn() => $this->gestures->isNotEmpty()
            ),
            'has_progress' => $this->when(
                $this->relationLoaded('progress'),
                fn() => $this->progress->isNotEmpty()
            ),
            
            // Metadatos
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'resource_type' => 'lesson',
                'api_version' => '1.0',
                'timestamp' => now()->toISOString()
            ]
        ];
    }
}