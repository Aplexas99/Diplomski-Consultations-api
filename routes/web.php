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


Route::get("test", function () {
    dd("test");
    return "test";
});
Route::get('home', function () {
    
$id = $_SESSION['login_id'];

echo($id);
    $user = [
        'id' => $id,
        'name' => 'Toni Dzoic',
        'email' => '',
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $user['name']; ?> - LaravelTuts</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }
        body{
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7ff;
            padding: 10px;
            margin: 0;
        }
        ._container{
            max-width: 400px;
            background-color: #ffffff;
            padding: 20px;
            margin: 0 auto;
            border: 1px solid #cccccc;
            border-radius: 2px;
        }
        .heading{
            text-align: center;
            color: #4d4d4d;
            text-transform: uppercase;
        }
        ._img{
            overflow: hidden;
            width: 100px;
            height: 100px;
            margin: 0 auto;
            border-radius: 50%;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        ._img > img{
            width: 100px;
            min-height: 100px;
        }
        ._info{
            text-align: center;
        }
        ._info h1{
            margin:10px 0;
            text-transform: capitalize;
        }
        ._info p{
            color: #555555;
        }
        ._info a{
            display: inline-block;
            background-color: #E53E3E;
            color: #fff;
            text-decoration: none;
            padding:5px 10px;
            border-radius: 2px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="_container">
        <h2 class="heading">My Account</h2>
    </div>
    <div class="_container">
        <div class="_img">
            <img src="<?php echo $user['profile_image']; ?>" alt="<?php echo $user['name']; ?>">
        </div>
        <div class="_info">
            <h1><?php echo $user['name']; ?></h1>
            <p><?php echo $user['email']; ?></p>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>
<?php
});
Route::get('/', function () {

if(isset($_SESSION['login_id'])){
    header('Location: home.php');
    exit;
}

// Creating new google client instance
$client = new Google_Client();

// Enter your Client ID
$client->setClientId('240437200182-04djpsevljl445a6v5ifujsjakp0t62m.apps.googleusercontent.com');
// Enter your Client Secrect
$client->setClientSecret('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');
// Enter the Redirect URL
$client->setRedirectUri('http://localhost:8000/home');

// Adding those scopes which we want to get (email & profile Information)
$client->addScope("email");
$client->addScope("profile");


if(isset($_GET['code'])):

    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if(!isset($token["error"])){

        $client->setAccessToken($token['access_token']);

        // getting profile information
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
    
        // Storing data into database
        $id = $google_account_info->id;
        $full_name =trim($google_account_info->name);

                $_SESSION['login_id'] = $id; 
                header('Location: home.php');
                exit;

        }

    
    else{
        exit;
    }
    
else: 
    // Google Login Url = $client->createAuthUrl(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - LaravelTuts</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
        }
        body{
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7ff;
            padding: 10px;
            margin: 0;
        }
        ._container{
            max-width: 400px;
            background-color: #ffffff;
            padding: 20px;
            margin: 0 auto;
            border: 1px solid #cccccc;
            border-radius: 2px;
        }
        ._container.btn{
            text-align: center;
        }
        .heading{
            text-align: center;
            color: #4d4d4d;
            text-transform: uppercase;
        }
        .login-with-google-btn {
            transition: background-color 0.3s, box-shadow 0.3s;
            padding: 12px 16px 12px 42px;
            border: none;
            border-radius: 3px;
            box-shadow: 0 -1px 0 rgb(0 0 0 / 4%), 0 1px 1px rgb(0 0 0 / 25%);
            color: #ffffff;
            font-size: 14px;
            font-weight: 500;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
            background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTgiIGhlaWdodD0iMTgiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj48cGF0aCBkPSJNMTcuNiA5LjJsLS4xLTEuOEg5djMuNGg0LjhDMTMuNiAxMiAxMyAxMyAxMiAxMy42djIuMmgzYTguOCA4LjggMCAwIDAgMi42LTYuNnoiIGZpbGw9IiM0Mjg1RjQiIGZpbGwtcnVsZT0ibm9uemVybyIvPjxwYXRoIGQ9Ik05IDE4YzIuNCAwIDQuNS0uOCA2LTIuMmwtMy0yLjJhNS40IDUuNCAwIDAgMS04LTIuOUgxVjEzYTkgOSAwIDAgMCA4IDV6IiBmaWxsPSIjMzRBODUzIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz48cGF0aCBkPSJNNCAxMC43YTUuNCA1LjQgMCAwIDEgMC0zLjRWNUgxYTkgOSAwIDAgMCAwIDhsMy0yLjN6IiBmaWxsPSIjRkJCQzA1IiBmaWxsLXJ1bGU9Im5vbnplcm8iLz48cGF0aCBkPSJNOSAzLjZjMS4zIDAgMi41LjQgMy40IDEuM0wxNSAyLjNBOSA5IDAgMCAwIDEgNWwzIDIuNGE1LjQgNS40IDAgMCAxIDUtMy43eiIgZmlsbD0iI0VBNDMzNSIgZmlsbC1ydWxlPSJub256ZXJvIi8+PHBhdGggZD0iTTAgMGgxOHYxOEgweiIvPjwvZz48L3N2Zz4=);
            background-color: #4a4a4a;
            background-repeat: no-repeat;
            background-position: 12px 11px;
            text-decoration: none;
        }
        .login-with-google-btn:hover {
            box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.04), 0 2px 4px rgba(0, 0, 0, 0.25);
        }
        .login-with-google-btn:active {
            background-color: #000000;
        }
        .login-with-google-btn:focus {
            outline: none;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.04), 0 2px 4px rgba(0, 0, 0, 0.25), 0 0 0 3px #c8dafc;
        }
        .login-with-google-btn:disabled {
            filter: grayscale(100%);
            background-color: #ebebeb;
            box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.04), 0 1px 1px rgba(0, 0, 0, 0.25);
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="_container">
        <h2 class="heading">Login</h2>
    </div>
    <div class="_container btn">
        
        <a type="button" class="login-with-google-btn" href="<?php echo $client->createAuthUrl(); ?>">
            Sign in with Google
        </a>

    </div>
</body>
</html>


<?php endif; 
});
/*
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
/*
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
$event = $service->events->listEvents($calendarId);
dd($event);
});
*/  
