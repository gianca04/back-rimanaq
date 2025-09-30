<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GestureRequest;
use App\Models\Gesture;
use Illuminate\Http\JsonResponse;

class GestureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $gestures = Gesture::with('lesson')->get();
        
        return response()->json([
            'success' => true,
            'data' => $gestures
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GestureRequest $request): JsonResponse
    {
        $gesture = Gesture::create($request->validated());
        $gesture->load('lesson');

        return response()->json([
            'success' => true,
            'message' => 'Gesto creado exitosamente',
            'data' => $gesture
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Gesture $gesture): JsonResponse
    {
        $gesture->load('lesson');

        return response()->json([
            'success' => true,
            'data' => $gesture
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GestureRequest $request, Gesture $gesture): JsonResponse
    {
        $gesture->update($request->validated());
        $gesture->load('lesson');

        return response()->json([
            'success' => true,
            'message' => 'Gesto actualizado exitosamente',
            'data' => $gesture
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gesture $gesture): JsonResponse
    {
        $gesture->delete();

        return response()->json([
            'success' => true,
            'message' => 'Gesto eliminado exitosamente'
        ]);
    }

    /**
     * Get gestures for a specific lesson.
     */
    public function getByLesson($lessonId): JsonResponse
    {
        $gestures = Gesture::where('lesson_id', $lessonId)->get();

        return response()->json([
            'success' => true,
            'data' => $gestures
        ]);
    }
}
