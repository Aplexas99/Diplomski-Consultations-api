<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseStudentResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page') ?? 50;
        $students = Student::query();
        $students = $students->with('user')->with('courses');
        /** Filters */
        if($request->get('name')) {
            $students = $students->filterByName($request->get('name'));
        }
        if($request->get('jmbag')) {
            $students = $students->filterByJmbag($request->get('jmbag'));
        }
        /** Sorts */
        if($request->get('sort_by')) {
            $sortBy = $request->get('sort_by');
            $sortDirection = $request->get('sort_direction') ? $request->get('sort_direction') : 'asc';
            if($sortBy == 'name') {
                $students = $students->sortByName($sortDirection);
            }
            if($sortBy == 'jmbag') {
                $students = $students->sortByJmbag($sortDirection);
            }
        }

        $students = $students->paginate($perPage);
        return StudentResource::collection($students);
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
    public function store(StoreStudentRequest $request)
    {
        $student = Student::create($request->all());
        return new StudentResource($student);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load('user')->load('courses');
        return new StudentResource($student);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        return new StudentResource($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return new StudentResource($student);
    }

    public function getCourses(Request $request)
    {
        $perPage = $request->get('per_page') ?? 50;
        $user = User::find($request->user()->id);
    
        // Start building the query
        $query = $user->student->courses();
    
        // Apply filters
        if ($request->get('name')) {
            $query = $query->where('name', 'like', '%' . $request->get('name') . '%');
        }
    
        // Apply sorting
        if ($request->get('sort_by')) {
            $sortBy = $request->get('sort_by');
            $sortDirection = $request->get('sort_direction') ?? 'asc';
            if ($sortBy === 'name') {
                $query = $query->orderBy('name', $sortDirection);
            }
        }
    
        // Execute the query and paginate the results
        $courses = $query->paginate($perPage);
    
        return CourseResource::collection($courses);
    }
    
    public function addStudentToCourse(Student $student, Course $course)
    {
        $existingRecord = CourseStudent::where([
            'course_id' => $course->id,
            'student_id' => $student->id,
        ])->first();

        if($existingRecord) {
            return response()->json([
                'message' => 'Student already added to course',
            ], 400);
        }

        $student->courses()->attach($course);
        return new StudentResource($student);
    }

    public function removeStudentFromCourse(Student $student, Course $course)
    {
        $student->courses()->detach($course);
        return new StudentResource($student);
    }

}
