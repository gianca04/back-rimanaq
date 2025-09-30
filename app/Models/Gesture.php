<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gesture extends Model
{
    /** @use HasFactory<\Database\Factories\GestureFactory> */
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'gesture_data',
    ];

    protected $casts = [
        'gesture_data' => 'array', // Automáticamente convierte JSON a array y viceversa
    ];

    /**
     * Get the lesson that owns the gesture.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
