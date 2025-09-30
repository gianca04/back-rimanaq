<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Progress;

describe('Course Model', function () {
    it('can be created with valid data', function () {
        $course = Course::factory()->create([
            'name' => 'Lenguaje de Señas Básico',
            'description' => 'Curso introductorio al lenguaje de señas',
            'color' => '#FF5722'
        ]);

        expect($course->name)->toBe('Lenguaje de Señas Básico');
        expect($course->description)->toBe('Curso introductorio al lenguaje de señas');
        expect($course->color)->toBe('#FF5722');
        expect($course)->toBeInstanceOf(Course::class);
    });

    it('has mass assignable attributes', function () {
        $course = new Course();
        $fillable = $course->getFillable();

        expect($fillable)->toContain('name');
        expect($fillable)->toContain('description');
        expect($fillable)->toContain('image_path');
        expect($fillable)->toContain('color');
    });

    it('has a lessons relationship', function () {
        $course = Course::factory()->create();

        expect($course->lessons())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    it('has a progress relationship', function () {
        $course = Course::factory()->create();

        expect($course->progress())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    it('can have multiple lessons', function () {
        $course = Course::factory()->create();
        $lesson1 = Lesson::factory()->create(['course_id' => $course->id]);
        $lesson2 = Lesson::factory()->create(['course_id' => $course->id]);

        expect($course->lessons)->toHaveCount(2);
        expect($course->lessons->first()->id)->toBe($lesson1->id);
        expect($course->lessons->last()->id)->toBe($lesson2->id);
    });

    it('can have multiple progress records', function () {
        $course = Course::factory()->create();
        $progress1 = Progress::factory()->create(['course_id' => $course->id]);
        $progress2 = Progress::factory()->create(['course_id' => $course->id]);

        expect($course->progress)->toHaveCount(2);
        expect($course->progress->first()->id)->toBe($progress1->id);
        expect($course->progress->last()->id)->toBe($progress2->id);
    });

    it('has timestamps', function () {
        $course = Course::factory()->create();

        expect($course->created_at)->not->toBeNull();
        expect($course->updated_at)->not->toBeNull();
        expect($course->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
        expect($course->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('requires a name', function () {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Course::create([
            'description' => 'Test description',
            'color' => '#FF5722'
        ]);
    });

    it('can be updated', function () {
        $course = Course::factory()->create(['name' => 'Original Name']);
        
        $course->update(['name' => 'Updated Name']);
        
        expect($course->fresh()->name)->toBe('Updated Name');
    });

    it('can be deleted', function () {
        $course = Course::factory()->create();
        $courseId = $course->id;
        
        $course->delete();
        
        expect(Course::find($courseId))->toBeNull();
    });
});