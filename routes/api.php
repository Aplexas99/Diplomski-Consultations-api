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


Route::group(['prefix' => 'google-calendar'], function () {
    Route::get('authenticate', [GoogleCalendarController::class, 'authenticate']);
    Route::get('callback', [GoogleCalendarController::class, 'callback']);
    Route::get('events', [GoogleCalendarController::class, 'getEvents']);
});


Route::get('index', function () {
    require_once 'C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\vendor\autoload.php';
    session_start();
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.storage_path('app/google-calendar/service-account-credentials.json').'');
    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();//BUSCARÁ GOOGLE_APLICATION_CREDENTIALS EN LA CONFIGURACION (.ENV)
    $client->addScope(Google\Service\Calendar::CALENDAR_EVENTS);

$client = new Google\Client();
$client->setAuthConfig('C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\storage\app\client_secret.json');
$client->addScope(Google\Service\Calendar::CALENDAR);

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
echo("Access token is set");
  $client->setAccessToken($_SESSION['access_token']);
  $calendar = new Google\Service\Calendar($client);
  $events = $calendar->events->listEvents('primary');

} else {
    echo("Access token is not set");
    $client = new Google\Client();
$client->setAuthConfigFile('C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\storage\app\client_secret.json');
$client->addScope(Google\Service\Calendar::CALENDAR_READONLY);


if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  echo("Redirecting to Google Calendar API..." . $auth_url);
  return $auth_url;
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  echo("Access token is set");
}
}
    
    $service = new Google\Service\Calendar($client);
    $calendarId = 'toni_dzoic@hotmail.com';
    $title = "Evento 21 de agosto, 22xx";
    $description = "Creado desde Laravel para el 21 de agosto";
    $startDateTime = new EventDateTime();
    $startDateTime->setDateTime(Carbon::now()->addDays(1)->addHour());
    $endDateTime =  new EventDateTime();
    $endDateTime->setDateTime(Carbon::now()->addDays(1)->addHour()->addMinutes(30));
    ##FIN_VARIABLES
    $event = new Google\Service\Calendar\Event();
    //add title, start time, end time, description, location to event and add conference data
    $event->setSummary($title);
    $event->setStart($startDateTime);
    $event->setEnd($endDateTime);
    $event->setDescription($description);
    $event->setLocation('Calle 123, Lima, Perú');
    $event->setConferenceData(new \Google\Service\Calendar\ConferenceData());
    
    //add attendees to event
    $attendee1 = new Google\Service\Calendar\EventAttendee();
    $attendee1->setEmail('toni_dzoic@hotmail.com');
    $attendees = array($attendee1);
    //$event->setAttendees($attendees);
    //set conference type to hangout
    $conference = new \Google\Service\Calendar\ConferenceData();
    $conferenceRequest = new \Google\Service\Calendar\CreateConferenceRequest();
    $conferenceRequest->setRequestId('3whatisup');
    $conferenceSolutionKey = new \Google\Service\Calendar\ConferenceSolutionKey();
    $conferenceSolutionKey->setType("Hangouts");
    $conferenceRequest->setConferenceSolutionKey(
        $conferenceSolutionKey
    );
    $e = new Spatie\GoogleCalendar\Event();
    $e->name = "Test";
    $e->description = "Test";
    $e->startDateTime = Carbon::now()->addDays(1)->addHour();
    $e->endDateTime = Carbon::now()->addDays(1)->addHour()->addMinutes(30);
    $e->addMeetLink();
    $e->save();
    $conference->setCreateRequest($conferenceRequest);
    $event->setConferenceData($conference);
    //insert event
    $event = $service->events->insert($calendarId, $event, array('conferenceDataVersion' => 1));
    dd($event);
});
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
      Route::put('consultation-requests/{consultationRequest}/accept', [ConsultationRequestController::class, 'acceptConsultationRequest']);
      Route::put('consultation-requests/{consultationRequest}/reject', [ConsultationRequestController::class, 'rejectConsultationRequest']);    
    });
    Route::group(['middleware' => 'student'], function (){
      Route::get('consultation-requests/scheduled', [ConsultationRequestController::class, 'getScheduledConsultationRequests']);
      Route::get('consultation-requests/pending', [ConsultationRequestController::class, 'getPendingConsultationRequests']);    
    Route::get("student/courses", [StudentController::class, 'getCourses']);

    });
    Route::post('logout', [ AuthController::class, 'logout' ]);
    Route::get('user-details', [ UserController::class, 'getLoggedUser' ]);

  });
