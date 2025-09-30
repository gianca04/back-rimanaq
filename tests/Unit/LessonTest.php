<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Gesture;
use App\Models\Progress;

describe('Lesson Model', function () {
    it('can be created with valid data', function () {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'name' => 'Lección de Saludos',
            'level_number' => 1,
            'description' => 'Aprende saludos básicos',
            'difficulty' => 'beginner',
            'time_minutes' => 30
        ]);

        expect($lesson->course_id)->toBe($course->id);
        expect($lesson->name)->toBe('Lección de Saludos');
        expect($lesson->level_number)->toBe(1);
        expect($lesson->description)->toBe('Aprende saludos básicos');
        expect($lesson->difficulty)->toBe('beginner');
        expect($lesson->time_minutes)->toBe(30);
        expect($lesson)->toBeInstanceOf(Lesson::class);
    });

    it('has mass assignable attributes', function () {
        $lesson = new Lesson();
        $fillable = $lesson->getFillable();

        expect($fillable)->toContain('course_id');
        expect($fillable)->toContain('name');
        expect($fillable)->toContain('level_number');
        expect($fillable)->toContain('description');
        expect($fillable)->toContain('difficulty');
        expect($fillable)->toContain('time_minutes');
    });

    it('casts level_number and time_minutes to integers', function () {
        $lesson = Lesson::factory()->create([
            'level_number' => '5',
            'time_minutes' => '45'
        ]);

        expect($lesson->level_number)->toBeInt();
        expect($lesson->time_minutes)->toBeInt();
        expect($lesson->level_number)->toBe(5);
        expect($lesson->time_minutes)->toBe(45);
    });

    it('belongs to a course', function () {
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);

        expect($lesson->course())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
        expect($lesson->course->id)->toBe($course->id);
    });

    it('has a gestures relationship', function () {
        $lesson = Lesson::factory()->create();

        expect($lesson->gestures())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    it('has a progress relationship', function () {
        $lesson = Lesson::factory()->create();

        expect($lesson->progress())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    it('can have multiple gestures', function () {
        $lesson = Lesson::factory()->create();
        $gesture1 = Gesture::factory()->create(['lesson_id' => $lesson->id]);
        $gesture2 = Gesture::factory()->create(['lesson_id' => $lesson->id]);

        expect($lesson->gestures)->toHaveCount(2);
        expect($lesson->gestures->first()->id)->toBe($gesture1->id);
        expect($lesson->gestures->last()->id)->toBe($gesture2->id);
    });

    it('can have multiple progress records', function () {
        $lesson = Lesson::factory()->create();
        $progress1 = Progress::factory()->create(['lesson_id' => $lesson->id]);
        $progress2 = Progress::factory()->create(['lesson_id' => $lesson->id]);

        expect($lesson->progress)->toHaveCount(2);
        expect($lesson->progress->first()->id)->toBe($progress1->id);
        expect($lesson->progress->last()->id)->toBe($progress2->id);
    });

    it('requires a course_id', function () {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Lesson::create([
            'name' => 'Test Lesson',
            'level_number' => 1,
            'difficulty' => 'beginner'
        ]);
    });

    it('requires a name', function () {
        $course = Course::factory()->create();
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Lesson::create([
            'course_id' => $course->id,
            'level_number' => 1,
            'difficulty' => 'beginner'
        ]);
    });

    it('has timestamps', function () {
        $lesson = Lesson::factory()->create();

        expect($lesson->created_at)->not->toBeNull();
        expect($lesson->updated_at)->not->toBeNull();
        expect($lesson->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
        expect($lesson->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('can be updated', function () {
        $lesson = Lesson::factory()->create(['name' => 'Original Lesson']);
        
        $lesson->update(['name' => 'Updated Lesson']);
        
        expect($lesson->fresh()->name)->toBe('Updated Lesson');
    });

    it('can be deleted', function () {
        $lesson = Lesson::factory()->create();
        $lessonId = $lesson->id;
        
        $lesson->delete();
        
        expect(Lesson::find($lessonId))->toBeNull();
    });
});