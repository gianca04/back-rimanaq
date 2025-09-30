<?php

use App\Models\Course;
use App\Models\Lesson;

// Crear un curso
$course = Course::create([
    'name' => 'Curso Básico LSP',
    'description' => 'Introducción a la Lengua de Señas Peruana',
    'color' => '#3498db'
]);

// Crear lecciones
Lesson::create([
    'course_id' => $course->id,
    'name' => 'Saludos básicos',
    'level_number' => 1,
    'description' => 'Aprende los saludos más comunes en LSP',
    'difficulty' => 'fácil',
    'time_minutes' => 15
]);

Lesson::create([
    'course_id' => $course->id,
    'name' => 'Números del 1 al 10',
    'level_number' => 2,
    'description' => 'Aprende a hacer los números en LSP',
    'difficulty' => 'fácil',
    'time_minutes' => 20
]);

Lesson::create([
    'course_id' => $course->id,
    'name' => 'Despedidas',
    'level_number' => 3,
    'description' => 'Formas de despedirse en LSP',
    'difficulty' => 'fácil',
    'time_minutes' => 10
]);

echo "Datos de prueba creados exitosamente!\n";
echo "Curso ID: " . $course->id . "\n";
echo "Total lecciones: " . Lesson::count() . "\n";