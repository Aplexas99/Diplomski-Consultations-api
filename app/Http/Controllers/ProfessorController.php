<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfessorRequest;
use App\Http\Requests\UpdateProfessorRequest;
use App\Http\Resources\ProfessorResource;
use App\Models\Course;
use App\Models\CourseProfessor;
use App\Models\Professor;
use App\Models\User;
use Illuminate\Http\Request;

class ProfessorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page') ?? 50;
        $professors = Professor::query();
        $professors = $professors->with('user')->with('courses');
        /** Filters */
        if ($request->get('name')) {
            $professors = $professors->filterByName($request->get('name'));
        }
        if ($request->get('last_name')) {
            $professors = $professors->filterByLastName($request->get('last_name'));
        }
        if ($request->get('email')) {
            $professors = $professors->filterByEmail($request->get('email'));
        }
        
        /** Sorts */
        if($request->get('sort_by')) {
            $sortBy = $request->get('sort_by');
            $sortDirection = $request->get('sort_direction') ? $request->get('sort_direction') : 'asc';
            if($sortBy == 'name') {
                $professors = $professors->sortByName($sortDirection);
            }
            if($sortBy == 'last-name') {
                $professors = $professors->sortByLastName($sortDirection);
            }
            if($sortBy == 'email') {
                $professors = $professors->sortByEmail($sortDirection);
            }
        }

        $professors = $professors->paginate($perPage);

        return ProfessorResource::collection($professors);
    }

    public function show(Professor $professor)
    {
        return new ProfessorResource($professor);
    }

    public function store(StoreProfessorRequest $request)
    {
        $professor = Professor::create([
            'user_id' => $request->get('user_id'),
        ]);
        
        return new ProfessorResource($professor);
    }

    public function update(UpdateProfessorRequest $request, Professor $professor)
    {
        $professor->user->update([
            'name' => $request->get('name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
        ]);
        
        return new ProfessorResource($professor);
    }
    public function destroy(Professor $professor)
    {
        $professor->delete();
        return new ProfessorResource($professor);
    }

    public function addCourseToProfessor(Professor $professor, Course $course)
    {
        $existingRecord = CourseProfessor::where([
            'professor_id' => $professor->id,
            'course_id' => $course->id,
        ])->first();
    
        if ($existingRecord) {
            return response()->json(['message' => 'Professor and course pair already exists'], 409); // Conflict status code
        }
    
        $professor->courses()->attach($course);
        return new ProfessorResource($professor);
    }
    public function removeCourseFromProfessor(Professor $professor, Course $course)
    {
        $professor->courses()->detach($course);
        return new ProfessorResource($professor);
    }
}
