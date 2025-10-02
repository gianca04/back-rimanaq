<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $lessons = Lesson::with('course')
                ->orderBy('course_id')
                ->orderBy('level_number')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $lessons,
                'meta' => [
                    'total' => $lessons->count(),
                    'message' => 'Lecciones obtenidas exitosamente'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener lecciones: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'No se pudieron obtener las lecciones'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LessonRequest $request): JsonResponse
    {
        try {
            // Verificar que el curso existe
            $course = Course::findOrFail($request->validated()['course_id']);
            
            // Verificar si ya existe una lección con el mismo nivel en el curso
            $existingLesson = Lesson::where('course_id', $course->id)
                ->where('level_number', $request->validated()['level_number'])
                ->first();
                
            if ($existingLesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una lección con ese nivel en el curso',
                    'errors' => [
                        'level_number' => ['Ya existe una lección con el nivel ' . $request->validated()['level_number'] . ' en este curso']
                    ]
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $lesson = Lesson::create($request->validated());
            $lesson->load('course');

            return response()->json([
                'success' => true,
                'message' => 'Lección creada exitosamente',
                'data' => $lesson
            ], Response::HTTP_CREATED);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El curso especificado no existe',
                'errors' => [
                    'course_id' => ['El curso seleccionado no existe']
                ]
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Exception $e) {
            Log::error('Error al crear lección: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'No se pudo crear la lección'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson): JsonResponse
    {
        try {
            $lesson->load(['course', 'gestures']);

            return response()->json([
                'success' => true,
                'data' => $lesson,
                'meta' => [
                    'gestures_count' => $lesson->gestures->count(),
                    'message' => 'Lección obtenida exitosamente'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener lección: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'No se pudo obtener la lección'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LessonRequest $request, Lesson $lesson): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            // Verificar si se está cambiando el course_id y si existe
            if (isset($validatedData['course_id'])) {
                Course::findOrFail($validatedData['course_id']);
            }
            
            // Verificar conflicto de nivel si se está cambiando
            if (isset($validatedData['level_number'])) {
                $courseId = $validatedData['course_id'] ?? $lesson->course_id;
                $existingLesson = Lesson::where('course_id', $courseId)
                    ->where('level_number', $validatedData['level_number'])
                    ->where('id', '!=', $lesson->id)
                    ->first();
                    
                if ($existingLesson) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe otra lección con ese nivel en el curso',
                        'errors' => [
                            'level_number' => ['Ya existe otra lección con el nivel ' . $validatedData['level_number'] . ' en este curso']
                        ]
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
            
            $lesson->update($validatedData);
            $lesson->load('course');

            return response()->json([
                'success' => true,
                'message' => 'Lección actualizada exitosamente',
                'data' => $lesson
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El curso especificado no existe',
                'errors' => [
                    'course_id' => ['El curso seleccionado no existe']
                ]
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Exception $e) {
            Log::error('Error al actualizar lección: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'No se pudo actualizar la lección'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson): JsonResponse
    {
        try {
            // Verificar si la lección tiene gestos asociados
            if ($lesson->gestures()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la lección porque tiene gestos asociados',
                    'errors' => [
                        'gestures' => ['Elimine primero todos los gestos de esta lección']
                    ]
                ], Response::HTTP_CONFLICT);
            }
            
            // Verificar si la lección tiene progreso asociado
            if ($lesson->progress()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la lección porque tiene progreso de usuarios asociado',
                    'errors' => [
                        'progress' => ['Esta lección tiene progreso de usuarios registrado']
                    ]
                ], Response::HTTP_CONFLICT);
            }
            
            $lessonName = $lesson->name;
            $lesson->delete();

            return response()->json([
                'success' => true,
                'message' => "Lección '{$lessonName}' eliminada exitosamente"
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al eliminar lección: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'No se pudo eliminar la lección'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get lessons for a specific course.
     */
    public function getByCourse($courseId): JsonResponse
    {
        try {
            // Verificar que el curso existe
            $course = Course::findOrFail($courseId);
            
            $lessons = Lesson::where('course_id', $courseId)
                ->with('course')
                ->orderBy('level_number')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $lessons,
                'meta' => [
                    'course' => [
                        'id' => $course->id,
                        'name' => $course->name
                    ],
                    'total_lessons' => $lessons->count(),
                    'message' => 'Lecciones del curso obtenidas exitosamente'
                ]
            ]);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El curso especificado no existe',
                'error' => 'Curso no encontrado'
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener lecciones del curso: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'No se pudieron obtener las lecciones del curso'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
