<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\CourseProfessor;
use App\Models\CourseStudent;
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
        return $course;
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return $course;
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
        return $course;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return $course;
    }

    public function getStudents(Request $request, int $courseId)
    {
        $course = Course::findOrFail($courseId);
        $students = $course->students;
        return $students;
    }
    public function addStudentToCourse(Request $request, int $courseId, int $studentId)
    {
        $existingRecord = CourseStudent::where([
            'course_id' => $courseId,
            'student_id' => $studentId,
        ])->first();

        if($existingRecord) {
            return response()->json([
                'message' => 'Student already added to course',
            ], 400);
        }

        CourseStudent::create([
            'course_id' => $courseId,
            'student_id' => $studentId,
        ]);
        return response()->json([
            'message' => 'Student added to course',
        ]);
    }

    public function removeStudentFromCourse(Request $request, int $courseId, int $studentId)
    {
        $course = Course::findOrFail($courseId);
        $course->students()->detach($studentId);
        return response()->json([
            'message' => 'Student removed from course',
        ]);
    }

    public function getProfessors(Request $request, int $courseId)
    {
        $course = Course::findOrFail($courseId);
        $professors = $course->professors;
        return $professors;
    }
    public function addProfessorToCourse(Request $request, int $courseId, int $professorId)
    {
        $existingRecord = CourseProfessor::where([
            'course_id' => $courseId,
            'professor_id' => $professorId,
        ])->first();

        if($existingRecord) {
            return response()->json([
                'message' => 'Professor already added to course',
            ], 400);
        }

        CourseProfessor::create([
            'course_id' => $courseId,
            'professor_id' => $professorId,
        ]);
        return response()->json([
            'message' => 'Professor added to course',
        ]);
    }

    public function removeProfessorFromCourse(Request $request, int $courseId, int $professorId)
    {
        $course = Course::findOrFail($courseId);
        $course->professors()->detach($professorId);
        return response()->json([
            'message' => 'Professor removed from course',
        ]);
    }
}
