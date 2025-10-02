<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LessonCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($lesson) use ($request) {
                return new LessonResource($lesson);
            }),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        $statistics = $this->calculateStatistics();
        
        return [
            'meta' => [
                'total' => $this->collection->count(),
                'statistics' => $statistics,
                'resource_type' => 'lesson_collection',
                'api_version' => '1.0',
                'timestamp' => now()->toISOString(),
                'performance' => [
                    'query_time' => microtime(true) - (request()->attributes->get('start_time', microtime(true))),
                    'memory_usage' => memory_get_usage(true)
                ]
            ]
        ];
    }

    /**
     * Calculate statistics from the collection.
     */
    private function calculateStatistics(): array
    {
        $difficulties = $this->collection->groupBy('difficulty');
        $averageDuration = $this->collection->avg('time_minutes');
        $totalDuration = $this->collection->sum('time_minutes');
        
        return [
            'by_difficulty' => [
                'fácil' => $difficulties->get('fácil', collect())->count(),
                'intermedio' => $difficulties->get('intermedio', collect())->count(),
                'difícil' => $difficulties->get('difícil', collect())->count(),
            ],
            'duration' => [
                'average_minutes' => round($averageDuration, 2),
                'total_minutes' => $totalDuration,
                'formatted_total' => $this->formatDuration($totalDuration),
                'formatted_average' => $this->formatDuration($averageDuration)
            ],
            'levels' => [
                'min_level' => $this->collection->min('level_number'),
                'max_level' => $this->collection->max('level_number'),
                'unique_levels' => $this->collection->pluck('level_number')->unique()->count()
            ]
        ];
    }

    /**
     * Format duration in minutes to human readable format.
     */
    private function formatDuration($minutes): string
    {
        if ($minutes < 60) {
            return round($minutes) . ' min';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        if ($remainingMinutes == 0) {
            return $hours == 1 ? "1 hora" : "{$hours} horas";
        }
        
        return $hours == 1 
            ? "1 hora " . round($remainingMinutes) . " min"
            : "{$hours} horas " . round($remainingMinutes) . " min";
    }
}