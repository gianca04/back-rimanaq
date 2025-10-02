<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApiErrorHandler
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);
            
            // Si la respuesta es exitosa, añadir headers útiles
            if ($response->getStatusCode() < 400) {
                $response->headers->set('X-API-Version', '1.0');
                $response->headers->set('X-Response-Time', microtime(true) - LARAVEL_START);
            }
            
            return $response;
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Recurso no encontrado', [
                'model' => $e->getModel(),
                'ids' => $e->getIds(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Recurso no encontrado',
                'error' => 'El recurso solicitado no existe',
                'meta' => [
                    'error_code' => 'RESOURCE_NOT_FOUND',
                    'timestamp' => now()->toISOString()
                ]
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::info('Error de validación', [
                'errors' => $e->errors(),
                'input' => $request->except(['password', 'password_confirmation']),
                'url' => $request->fullUrl()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Los datos enviados no son válidos',
                'errors' => $e->errors(),
                'meta' => [
                    'error_code' => 'VALIDATION_ERROR',
                    'error_count' => count($e->errors()),
                    'timestamp' => now()->toISOString()
                ]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
            
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error de base de datos', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'url' => $request->fullUrl()
            ]);
            
            // Detectar errores comunes de base de datos
            $errorCode = $e->getCode();
            $message = 'Error en la base de datos';
            
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $message = 'Ya existe un registro con esos datos';
            } elseif (str_contains($e->getMessage(), 'foreign key constraint')) {
                $message = 'No se puede realizar la operación debido a referencias de datos';
            } elseif (str_contains($e->getMessage(), 'cannot be null')) {
                $message = 'Faltan datos obligatorios';
            }
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno del servidor',
                'meta' => [
                    'error_code' => 'DATABASE_ERROR',
                    'timestamp' => now()->toISOString()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
            
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            Log::info('Ruta no encontrada', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ruta no encontrada',
                'error' => 'El endpoint solicitado no existe',
                'meta' => [
                    'error_code' => 'ROUTE_NOT_FOUND',
                    'timestamp' => now()->toISOString()
                ]
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e) {
            Log::info('Método no permitido', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'allowed_methods' => $e->getHeaders()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Método HTTP no permitido',
                'error' => 'El método ' . $request->method() . ' no está permitido para esta ruta',
                'meta' => [
                    'error_code' => 'METHOD_NOT_ALLOWED',
                    'allowed_methods' => $e->getHeaders()['Allow'] ?? [],
                    'timestamp' => now()->toISOString()
                ]
            ], Response::HTTP_METHOD_NOT_ALLOWED);
            
        } catch (\Exception $e) {
            Log::error('Error no manejado', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'input' => $request->except(['password', 'password_confirmation'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Ha ocurrido un error inesperado',
                'meta' => [
                    'error_code' => 'INTERNAL_SERVER_ERROR',
                    'timestamp' => now()->toISOString()
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}