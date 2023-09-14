<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseProfessorResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\CourseProfessor;
use App\Models\CourseStudent;
use App\Models\Professor;
use App\Models\Student;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page') ?? 50;
        $courses = Course::query();
        $courses = $courses->with('professors')->with('students');
        /** Filters */
        if ($request->get('name')) {
            $courses = $courses->filterByName($request->get('name'));
        }
        
        /** Sorts */
        if($request->get('sort_by')) {
            $sortBy = $request->get('sort_by');
            $sortDirection = $request->get('sort_direction') ? $request->get('sort_direction') : 'asc';
            if($sortBy == 'name') {
                $courses = $courses->sortByName($sortDirection);
            }
        }

        $courses = $courses->paginate($perPage);
        return CourseResource::collection($courses);
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
    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());
        return new CourseResource($course);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        $course->load('professors')->load('students');
        return new CourseResource($course);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course->update($request->validated());
        return new CourseResource($course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return new CourseResource($course);
    }

    public function getStudents(Request $request, int $courseId)
    {
        $course = Course::findOrFail($courseId);
        $students = $course->students;
        return $students;
    }


    public function addStudentToCourse(Course $course, Student $student)
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

        $course->students()->attach($student);
        return new CourseResource($course);
    }

    public function removeStudentFromCourse(Course $course, Student $student)
    {
        $course->students()->detach($student->id);
        return new CourseResource($course);
    }

    public function getProfessors(Request $request, int $courseId)
    {
        $course = Course::findOrFail($courseId);
        $professors = $course->professors;
        return $professors;
    }
    public function addProfessorToCourse(Course $course, Professor $professor)
    {
        $existingRecord = CourseProfessor::where([
            'course_id' => $course->id,
            'professor_id' => $professor->id,
        ])->first();

        if($existingRecord) {
            return response()->json([
                'message' => 'Professor already added to course',
            ], 400);
        }

        $course->professors()->attach($professor);
        return new CourseResource($course);
    }

    public function removeProfessorFromCourse(Course $course, Professor $professor)
    {
        $course->professors()->detach($professor->id);
        return new CourseResource($course);
    }
}
