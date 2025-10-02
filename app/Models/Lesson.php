<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Lesson extends Model
{
    /** @use HasFactory<\Database\Factories\LessonFactory> */
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'level_number',
        'description',
        'difficulty',
        'time_minutes',
    ];

    protected $casts = [
        'level_number' => 'integer',
        'time_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'difficulty_label',
        'formatted_duration',
        'progress_count'
    ];

    /**
     * Get the course that owns the lesson.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the gestures for the lesson.
     */
    public function gestures(): HasMany
    {
        return $this->hasMany(Gesture::class);
    }

    /**
     * Get the progress records for the lesson.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * Scope: Filter lessons by difficulty.
     */
    public function scopeByDifficulty(Builder $query, string $difficulty): Builder
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope: Filter lessons by course.
     */
    public function scopeByCourse(Builder $query, int $courseId): Builder
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope: Order by level number.
     */
    public function scopeOrderByLevel(Builder $query): Builder
    {
        return $query->orderBy('level_number');
    }

    /**
     * Scope: Filter lessons by duration range.
     */
    public function scopeByDuration(Builder $query, int $minMinutes = null, int $maxMinutes = null): Builder
    {
        if ($minMinutes) {
            $query->where('time_minutes', '>=', $minMinutes);
        }
        
        if ($maxMinutes) {
            $query->where('time_minutes', '<=', $maxMinutes);
        }
        
        return $query;
    }

    /**
     * Scope: Get lessons with their course and gesture count.
     */
    public function scopeWithRelatedData(Builder $query): Builder
    {
        return $query->with(['course', 'gestures'])
            ->withCount('gestures');
    }

    /**
     * Accessor: Get formatted difficulty label.
     */
    protected function difficultyLabel(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $labels = [
                    'fácil' => 'Fácil',
                    'intermedio' => 'Intermedio',
                    'difícil' => 'Difícil'
                ];
                
                return $labels[$attributes['difficulty']] ?? ucfirst($attributes['difficulty']);
            }
        );
    }

    /**
     * Accessor: Get formatted duration.
     */
    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $minutes = $attributes['time_minutes'];
                
                if ($minutes < 60) {
                    return "{$minutes} min";
                }
                
                $hours = floor($minutes / 60);
                $remainingMinutes = $minutes % 60;
                
                if ($remainingMinutes === 0) {
                    return $hours === 1 ? "1 hora" : "{$hours} horas";
                }
                
                return $hours === 1 
                    ? "1 hora {$remainingMinutes} min"
                    : "{$hours} horas {$remainingMinutes} min";
            }
        );
    }

    /**
     * Accessor: Get progress count.
     */
    protected function progressCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->progress()->count()
        );
    }

    /**
     * Mutator: Normalize difficulty input.
     */
    protected function difficulty(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                $normalized = strtolower(trim($value));
                $difficultyMap = [
                    'facil' => 'fácil',
                    'fácil' => 'fácil',
                    'intermedio' => 'intermedio',
                    'dificil' => 'difícil',
                    'difícil' => 'difícil'
                ];
                
                return $difficultyMap[$normalized] ?? $value;
            }
        );
    }

    /**
     * Check if lesson has gestures.
     */
    public function hasGestures(): bool
    {
        return $this->gestures()->exists();
    }

    /**
     * Check if lesson has user progress.
     */
    public function hasProgress(): bool
    {
        return $this->progress()->exists();
    }

    /**
     * Get difficulty color for UI.
     */
    public function getDifficultyColorAttribute(): string
    {
        return match ($this->difficulty) {
            'fácil' => 'success',
            'intermedio' => 'warning',
            'difícil' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get level display name.
     */
    public function getLevelDisplayAttribute(): string
    {
        return "Nivel {$this->level_number}";
    }
}
