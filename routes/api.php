<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\ConsultationRequestController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Carbon\Carbon;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\Google_Service_Calendar;
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
Route::get('google/login/url', [GoogleCalendarController::class, 'getAuthUrl']);
Route::post('google/auth/login', [GoogleCalendarController::class, 'postLogin']);


Route::get('code', function () {  
  return redirect()->away('http://localhost:4200/app/google-signin?code='.$_GET['code'].'');
});

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
    Route::resource("consultation-requests", ConsultationRequestController::class);
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

Route::get('google/events', [GoogleCalendarController::class, 'getEvents']);
Route::post('google-calendar/events', [GoogleCalendarController::class, 'addEvent']);

Route::get('professor/{id}/booked-appointments', [ConsultationRequestController::class, 'getBookedAppointmentsForProfessor']);

Route::group(['middleware' => 'auth:sanctum'], function () {
  
  Route::get("courses", [CourseController::class, 'index']);
  Route::get('courses/{course}/professors', [CourseController::class, 'getProfessors']);
  Route::get('courses/{course}', [CourseController::class, 'show']);
  Route::get("professors", [ProfessorController::class, 'index']);
  Route::get('professors/{professor}/courses', [ProfessorController::class, 'getCourses']);
  Route::get('professors/{professor}', [ProfessorController::class, 'show']);

  Route::get("consultation-requests", [ConsultationRequestController::class, 'index']);
  Route::post('consultation-requests', [ConsultationRequestController::class, 'store']);
    Route::group(['middleware' => 'professor'], function (){
      Route::get('professor/scheduled', [ConsultationRequestController::class, 'getScheduledConsultationRequestsProfessor']);
      Route::get('professor/pending', [ConsultationRequestController::class, 'getPendingConsultationRequestsProfessor']);
      Route::get('professor/rejected', [ConsultationRequestController::class, 'getRejectedConsultationRequestsProfessor']);
      Route::put('consultation-requests/{consultationRequest}/accept', [ConsultationRequestController::class, 'acceptConsultationRequest']);
      Route::put('consultation-requests/{consultationRequest}/reject', [ConsultationRequestController::class, 'rejectConsultationRequest']);    
    });
    Route::group(['middleware' => 'student'], function (){
      Route::get('consultation-requests/scheduled', [ConsultationRequestController::class, 'getScheduledConsultationRequests']);
      Route::get('consultation-requests/pending', [ConsultationRequestController::class, 'getPendingConsultationRequests']);    
      Route::get('consultation-requests/rejected', [ConsultationRequestController::class, 'getRejectedConsultationRequests']);
      Route::get("student/courses", [StudentController::class, 'getCourses']);

    });
    Route::post('logout', [ AuthController::class, 'logout' ]);
    Route::get('user-details', [ UserController::class, 'getLoggedUser' ]);

  });
