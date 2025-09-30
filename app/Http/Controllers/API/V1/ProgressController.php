<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProgressRequest;
use App\Models\Progress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $progress = Progress::with(['user', 'lesson', 'course'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProgressRequest $request): JsonResponse
    {
        $progress = Progress::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'lesson_id' => $request->lesson_id
            ],
            $request->validated()
        );

        $progress->load(['user', 'lesson', 'course']);

        return response()->json([
            'success' => true,
            'message' => 'Progreso registrado exitosamente',
            'data' => $progress
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Progress $progress): JsonResponse
    {
        $progress->load(['user', 'lesson', 'course']);

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProgressRequest $request, Progress $progress): JsonResponse
    {
        $progress->update($request->validated());
        $progress->load(['user', 'lesson', 'course']);

        return response()->json([
            'success' => true,
            'message' => 'Progreso actualizado exitosamente',
            'data' => $progress
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Progress $progress): JsonResponse
    {
        $progress->delete();

        return response()->json([
            'success' => true,
            'message' => 'Progreso eliminado exitosamente'
        ]);
    }

    /**
     * Get progress for a specific user.
     */
    public function getByUser($userId): JsonResponse
    {
        $progress = Progress::where('user_id', $userId)
            ->with(['lesson', 'course'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    /**
     * Get progress for a specific course.
     */
    public function getByCourse($courseId): JsonResponse
    {
        $progress = Progress::where('course_id', $courseId)
            ->with(['user', 'lesson'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    /**
     * Get progress for a specific lesson.
     */
    public function getByLesson($lessonId): JsonResponse
    {
        $progress = Progress::where('lesson_id', $lessonId)
            ->with(['user', 'course'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $progress
        ]);
    }

    /**
     * Mark a lesson as completed for a user.
     */
    public function markCompleted(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'lesson_id' => 'required|exists:lessons,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $progress = Progress::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'lesson_id' => $request->lesson_id
            ],
            [
                'user_id' => $request->user_id,
                'lesson_id' => $request->lesson_id,
                'course_id' => $request->course_id,
                'completed' => true,
                'attempts_count' => DB::raw('attempts_count + 1')
            ]
        );

        $progress->load(['user', 'lesson', 'course']);

        return response()->json([
            'success' => true,
            'message' => 'Lección marcada como completada',
            'data' => $progress
        ]);
    }

    /**
     * Increment attempts for a lesson.
     */
    public function incrementAttempts(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'lesson_id' => 'required|exists:lessons,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $progress = Progress::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'lesson_id' => $request->lesson_id
            ],
            [
                'user_id' => $request->user_id,
                'lesson_id' => $request->lesson_id,
                'course_id' => $request->course_id,
                'attempts_count' => 1
            ]
        );

        if ($progress->wasRecentlyCreated) {
            $progress->attempts_count = 1;
        } else {
            $progress->increment('attempts_count');
        }

        $progress->load(['user', 'lesson', 'course']);

        return response()->json([
            'success' => true,
            'message' => 'Intento registrado',
            'data' => $progress
        ]);
    }
}
