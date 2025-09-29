<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $courses = Course::all();
        return response()->json($courses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        Course::create($request->post());
        return response()->json([
            'course' => $request->post(),
            'message' => 'Course created successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $course)
    {
        //
        return response()->json($course);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        //
        $course->fill($request->post())->save();
        return response()->json([
            'course' => $course,    
            'message' => 'Course updated successfully!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        //
        $course->delete();
        return response()->json([
            'message' => 'Course deleted successfully!'
        ]);
    }
}
