<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Controlador para las vistas web de cursos
 * Maneja la renderización de vistas y la comunicación con la API
 */
class CourseWebController extends Controller
{
    /**
     * URL base de la API
     */
    private string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.url') . '/api';
    }

    /**
     * Mostrar la lista de cursos
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('course.index', [
            'title' => 'Gestión de Cursos',
            'apiUrl' => $this->apiBaseUrl . '/courses',
            'createUrl' => route('web.courses.create')
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo curso
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('course.create', [
            'title' => 'Crear Nuevo Curso',
            'apiUrl' => $this->apiBaseUrl . '/courses',
            'indexUrl' => route('web.courses.index')
        ]);
    }

    /**
     * Mostrar el formulario para editar un curso
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return view('course.edit', [
            'title' => 'Editar Curso',
            'courseId' => $id,
            'apiUrl' => $this->apiBaseUrl . '/courses',
            'indexUrl' => route('web.courses.index')
        ]);
    }

    /**
     * Mostrar los detalles de un curso específico
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return view('course.show', [
            'title' => 'Detalles del Curso',
            'courseId' => $id,
            'apiUrl' => $this->apiBaseUrl . '/courses',
            'indexUrl' => route('web.courses.index'),
            'editUrl' => route('web.courses.edit', $id)
        ]);
    }
}