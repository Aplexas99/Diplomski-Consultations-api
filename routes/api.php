<?php

use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\ConsultationRequestController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource("users", UserController::class);
Route::resource("roles", RoleController::class);

Route::post("professors/{professor}/add-course/{course}", [ProfessorController::class, 'addCourseToProfessor']);
Route::post("professors/{professor}/remove-course/{course}", [ProfessorController::class, 'removeCourseFromProfessor']);
Route::resource("professors", ProfessorController::class);

Route::get("student/{student}/courses", [StudentController::class, 'getCourses']);
Route::post("students/{student}/add-course/{course}", [StudentController::class, 'addStudentToCourse']);
Route::delete("students/{student}/remove-course/{course}", [StudentController::class, 'removeStudentFromCourse']);
Route::resource("students", StudentController::class);
Route::resource("courses", CourseController::class);
Route::resource("schedules", ScheduleController::class);
Route::resource("consultation_requests", ConsultationRequestController::class);
Route::resource("chat_rooms", ChatRoomController::class);

Route::post('chat-rooms/{chatRoom}/add-professor/{professor}', [ChatRoomController::class, 'addProfessorToChatRoom']);
Route::post('chat-rooms/{chatRoom}/remove-professor/{professor}', [ChatRoomController::class, 'removeProfessorFromChatRoom']);

Route::post('chat-rooms/{chatRoom}/add-student/{student}', [ChatRoomController::class, 'addStudentToChatRoom']);
Route::post('chat-rooms/{chatRoom}/remove-student/{student}', [ChatRoomController::class, 'removeStudentFromChatRoom']);

Route::post('courses/{course}/add-professor/{professor}', [CourseController::class, 'addProfessorToCourse']);
Route::delete('courses/{course}/remove-professor/{professor}', [CourseController::class, 'removeProfessorFromCourse']);

Route::post('courses/{course}/add-student/{student}', [CourseController::class, 'addStudentToCourse']);
Route::delete('courses/{course}/remove-student/{student}', [CourseController::class, 'removeStudentFromCourse']);
