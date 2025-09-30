<?php

use App\Models\Lesson;
use App\Models\Gesture;
use App\Models\Course;

describe('Gesture Model', function () {
    it('can be created with valid data', function () {
        $lesson = Lesson::factory()->create();
        $gestureData = [
            'name' => 'Hola',
            'description' => 'Saludo básico',
            'keypoints' => [
                ['x' => 100, 'y' => 200, 'z' => 0],
                ['x' => 150, 'y' => 250, 'z' => 5]
            ]
        ];
        
        $gesture = Gesture::factory()->create([
            'lesson_id' => $lesson->id,
            'gesture_data' => $gestureData
        ]);

        expect($gesture->lesson_id)->toBe($lesson->id);
        expect($gesture->gesture_data)->toBe($gestureData);
        expect($gesture)->toBeInstanceOf(Gesture::class);
    });

    it('has mass assignable attributes', function () {
        $gesture = new Gesture();
        $fillable = $gesture->getFillable();

        expect($fillable)->toContain('lesson_id');
        expect($fillable)->toContain('gesture_data');
    });

    it('casts gesture_data to array', function () {
        $gestureData = [
            'name' => 'Gracias',
            'keypoints' => [
                ['x' => 100, 'y' => 200],
                ['x' => 150, 'y' => 250]
            ]
        ];
        
        $gesture = Gesture::factory()->create([
            'gesture_data' => $gestureData
        ]);

        expect($gesture->gesture_data)->toBeArray();
        expect($gesture->gesture_data['name'])->toBe('Gracias');
        expect($gesture->gesture_data['keypoints'])->toBeArray();
    });

    it('belongs to a lesson', function () {
        $lesson = Lesson::factory()->create();
        $gesture = Gesture::factory()->create(['lesson_id' => $lesson->id]);

        expect($gesture->lesson())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
        expect($gesture->lesson->id)->toBe($lesson->id);
    });

    it('can access lesson through relationship', function () {
        $course = Course::factory()->create(['name' => 'Test Course']);
        $lesson = Lesson::factory()->create([
            'course_id' => $course->id,
            'name' => 'Test Lesson'
        ]);
        $gesture = Gesture::factory()->create(['lesson_id' => $lesson->id]);

        expect($gesture->lesson->name)->toBe('Test Lesson');
        expect($gesture->lesson->course->name)->toBe('Test Course');
    });

    it('requires a lesson_id', function () {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Gesture::create([
            'gesture_data' => ['name' => 'Test Gesture']
        ]);
    });

    it('can store complex gesture data', function () {
        $complexData = [
            'name' => 'Adiós',
            'description' => 'Gesto de despedida',
            'difficulty' => 'easy',
            'keypoints' => [
                [
                    'landmark' => 'WRIST',
                    'x' => 0.5,
                    'y' => 0.6,
                    'z' => 0.1,
                    'visibility' => 0.9
                ],
                [
                    'landmark' => 'THUMB_TIP',
                    'x' => 0.6,
                    'y' => 0.5,
                    'z' => 0.0,
                    'visibility' => 0.8
                ]
            ],
            'metadata' => [
                'duration_ms' => 2000,
                'hand' => 'right'
            ]
        ];
        
        $gesture = Gesture::factory()->create([
            'gesture_data' => $complexData
        ]);

        expect($gesture->gesture_data['name'])->toBe('Adiós');
        expect($gesture->gesture_data['keypoints'])->toHaveCount(2);
        expect($gesture->gesture_data['metadata']['duration_ms'])->toBe(2000);
    });

    it('has timestamps', function () {
        $gesture = Gesture::factory()->create();

        expect($gesture->created_at)->not->toBeNull();
        expect($gesture->updated_at)->not->toBeNull();
        expect($gesture->created_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
        expect($gesture->updated_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('can be updated', function () {
        $originalData = ['name' => 'Original'];
        $updatedData = ['name' => 'Updated'];
        
        $gesture = Gesture::factory()->create(['gesture_data' => $originalData]);
        $gesture->update(['gesture_data' => $updatedData]);
        
        expect($gesture->fresh()->gesture_data['name'])->toBe('Updated');
    });

    it('can be deleted', function () {
        $gesture = Gesture::factory()->create();
        $gestureId = $gesture->id;
        
        $gesture->delete();
        
        expect(Gesture::find($gestureId))->toBeNull();
    });
});