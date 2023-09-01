<?php

use App\Http\Controllers\AuthController;
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

// login
Route::post('login', [ AuthController::class, 'login' ]);
Route::post('send-reset-password-link', [ AuthController::class, 'sendResetPasswordLink' ]);
Route::post('reset-password', [ AuthController::class, 'resetPassword' ])->name('password.reset');
Route::post('admin/login', [ AuthController::class, 'loginAdmin' ]);

Route::group(['middleware' => ['auth:sanctum','admin'], 'prefix' => 'admin'], function () {

    Route::resource("users", UserController::class);
    Route::resource("roles", RoleController::class);
    
    Route::post("professors/{professor}/add-course/{course}", [ProfessorController::class, 'addCourseToProfessor']);
    Route::post("professors/{professor}/remove-course/{course}", [ProfessorController::class, 'removeCourseFromProfessor']);
    Route::resource("professors", ProfessorController::class);
    
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
    
});



Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('user-details', [ UserController::class, 'getLoggedUser' ]);

    Route::group(['middleware' => 'professor', 'prefix' => 'professor'], function (){
     });
    
    Route::group(['middleware' => 'student', 'prefix' => 'student'], function (){
        Route::get("{user}/courses", [StudentController::class, 'getCourses']);
      });
    
    Route::post('logout', [ AuthController::class, 'logout' ]);

  });
