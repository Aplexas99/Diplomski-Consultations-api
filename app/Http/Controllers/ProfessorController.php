<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfessorRequest;
use App\Http\Requests\UpdateProfessorRequest;
use App\Models\Professor;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function index()
    {
        $professors = Professor::all();
        return $professors;
    }

    public function show(Professor $professor)
    {
        return $professor;
    }

    public function store(StoreProfessorRequest $request)
    {
        Professor::create($request->all());
        return response()->json([
            'message' => 'Professor created successfully',
            'professor' => $request->all()
        ], 201);
    }

    public function update(UpdateProfessorRequest $request, Professor $professor)
    {
        $professor->update($request->all());
        return response()->json([
            'message' => 'Professor updated successfully',
            'professor' => $professor
        ], 200);
    }
    public function destroy(Professor $professor)
    {
        $professor->delete();
        return $professor;
    }
}
