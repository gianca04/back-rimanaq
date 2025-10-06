<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class GestureWebController extends Controller
{
    public function index()
    {
        return view('gesture.index');
    }

    public function create()
    {
        return view('gesture.create');
    }

    public function edit($id)
    {
        return view('gesture.edit', compact('id'));
    }
}