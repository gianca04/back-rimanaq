<?php

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Progress;

describe('Progress Model', function () {
    it('can be created with valid data', function () {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $lesson = Lesson::factory()->create(['course_id' => $course->id]);
        
        $progress = Progress::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'course_id' => $course->id,
            'completed' => true,
            'attempts_count' => 3
        ]);

        expect($progress->user_id)->toBe($user->id);
        expect($progress->lesson_id)->toBe($lesson->id);
        expect($progress->course_id)->toBe($course->id);
        expect($progress->completed)->toBeTrue();
        expect($progress->attempts_count)->toBe(3);
        expect($progress)->toBeInstanceOf(Progress::class);
    });

    it('has mass assignable attributes', function () {
        $progress = new Progress();
        $fillable = $progress->getFillable();

        expect($fillable)->toContain('user_id');
        expect($fillable)->toContain('lesson_id');
        expect($fillable)->toContain('course_id');
        expect($fillable)->toContain('completed');
        expect($fillable)->toContain('attempts_count');
    });

    it('uses the correct table name', function () {
        $progress = new Progress();
        expect($progress->getTable())->toBe('progress');
    });

    it('casts completed to boolean and attempts_count to integer', function () {
        $progress = Progress::factory()->create([
            'completed' => '1',
            'attempts_count' => '5'
        ]);

        expect($progress->completed)->toBeBoolean();
        expect($progress->attempts_count)->toBeInt();
        expect($progress->completed)->toBeTrue();
        expect($progress->attempts_count)->toBe(5);
    });

    it('belongs to a user', function () {
        $user = User::factory()->create();
        $progress = Progress::factory()->create(['user_id' => $user->id]);

        expect($progress->user())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
        expect($progress->user->id)->toBe($user->id);
    });

    it('belongs to a lesson', function () {
        $lesson = Lesson::factory()->create();
        $progress = Progress::factory()->create(['lesson_id' => $lesson->id]);

        expect($progress->lesson())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
        expect($progress->lesson->id)->toBe($lesson->id);
    });

    it('belongs to a course', function () {
        $course = Course::factory()->create();
        $progress = Progress::factory()->create(['course_id' => $course->id]);

        expect($progress->course())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
        expect($progress->course->id)->toBe($course->id);
    });

    it('can access related models through relationships', function () {
        $user = User::factory()->create(['name' => 'Test User']);
        $course = Course::factory()->create(['name' => 'Test Course']);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'name' => 'Test Lesson'
        ]);
        
        $progress = Progress::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'course_id' => $course->id
        ]);

        expect($progress->user->name)->toBe('Test User');
        expect($progress->lesson->name)->toBe('Test Lesson');
        expect($progress->course->name)->toBe('Test Course');
    });

    it('requires a user_id', function () {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Progress::create([
            'lesson_id' => 1,
            'course_id' => 1,
            'completed' => false
        ]);
    });

    it('requires a lesson_id', function () {
        $user = User::factory()->create();
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Progress::create([
            'user_id' => $user->id,
            'course_id' => 1,
            'completed' => false
        ]);
    });

    it('requires a course_id', function () {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Progress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'completed' => false
        ]);
    });

    it('defaults completed to false when not specified', function () {
        $progress = Progress::factory()->create(['completed' => null]);
        
        expect($progress->completed)->toBeFalse();
    });

    it('defaults attempts_count to 0 when not specified', function () {
        $progress = Progress::factory()->create(['attempts_count' => null]);
        
        expect($progress->attempts_count)->toBe(0);
    });

    it('has timestamps', function () {
        $progress = Progress::factory()->create();

        expect($progress->created_at)->not->toBeNull();
        expect($progress->updated_at)->not->toBeNull();
        expect($progress->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
        expect($progress->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('can be updated', function () {
        $progress = Progress::factory()->create(['completed' => false, 'attempts_count' => 1]);
        
        $progress->update(['completed' => true, 'attempts_count' => 3]);
        
        $fresh = $progress->fresh();
        expect($fresh->completed)->toBeTrue();
        expect($fresh->attempts_count)->toBe(3);
    });

    it('can be deleted', function () {
        $progress = Progress::factory()->create();
        $progressId = $progress->id;
        
        $progress->delete();
        
        expect(Progress::find($progressId))->toBeNull();
    });
});