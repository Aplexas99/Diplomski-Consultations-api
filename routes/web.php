<?php

use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    putenv('GOOGLE_APPLICATION_CREDENTIALS='.storage_path('app/google-calendar/service-account-credentials.json').'');
$client = new Google_Client();
$client->useApplicationDefaultCredentials();//BUSCARÁ GOOGLE_APLICATION_CREDENTIALS EN LA CONFIGURACION (.ENV)
$client->addScope(Google_Service_Calendar::CALENDAR);

###Instanciamos el servicio en base a $client
$service = new Google_Service_Calendar($client);
##SIEMPRE OBTENDREMOS EL ID DE CALENDARIO
$calendarId = 'toni_dzoic@hotmail.com';//EL ID DEL CALENDARIO CUANDO COMPARTIMOS Y CONFIGURAMOS UN CALENDARIO DESDE EL PORTAL DE GOOGLE CALENDAR


####crear
##VARIABLES
$title = "Evento 21 de agosto, 22xx";
$description = "Creado desde Laravel para el 21 de agosto";
$startDateTime = new \Google\Service\Calendar\EventDateTime();
$startDateTime->setDateTime(Carbon\Carbon::now()->addDays(1)->addHour());
$endDateTime =  new \Google\Service\Calendar\EventDateTime();
$endDateTime->setDateTime(Carbon\Carbon::now()->addDays(1)->addHour()->addMinutes(30));
##FIN_VARIABLES
$event = new Google_Service_Calendar_Event();
//add title, start time, end time, description, location to event and add conference data
$event->setSummary($title);
$event->setStart($startDateTime);
$event->setEnd($endDateTime);
$event->setDescription($description);
$event->setLocation('Calle 123, Lima, Perú');
$event->setConferenceData(new \Google\Service\Calendar\ConferenceData());

//add attendees to event
$attendee1 = new Google_Service_Calendar_EventAttendee();
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
$e->startDateTime = Carbon\Carbon::now()->addDays(1)->addHour();
$e->endDateTime = Carbon\Carbon::now()->addDays(1)->addHour()->addMinutes(30);
$e->addMeetLink();
$e->save();
$conference->setCreateRequest($conferenceRequest);
$event->setConferenceData($conference);
//insert event
$event = $service->events->insert($calendarId, $event, array('conferenceDataVersion' => 1));
dd($event);
});
