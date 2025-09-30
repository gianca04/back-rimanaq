<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $lessons = Lesson::with('course')->get();
        
        return response()->json([
            'success' => true,
            'data' => $lessons
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request): JsonResponse
    {
        $lesson = Lesson::create($request->validated());
        $lesson->load('course');

        return response()->json([
            'success' => true,
            'message' => 'Lección creada exitosamente',
            'data' => $lesson
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson): JsonResponse
    {
        $lesson->load('course');

        return response()->json([
            'success' => true,
            'data' => $lesson
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LessonRequest $request, Lesson $lesson): JsonResponse
    {
        $lesson->update($request->validated());
        $lesson->load('course');

        return response()->json([
            'success' => true,
            'message' => 'Lección actualizada exitosamente',
            'data' => $lesson
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson): JsonResponse
    {
        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lección eliminada exitosamente'
        ]);
    }

    /**
     * Get lessons for a specific course.
     */
    public function getByCourse($courseId): JsonResponse
    {
        $lessons = Lesson::where('course_id', $courseId)
            ->orderBy('level_number')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lessons
        ]);
    }
}
