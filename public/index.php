<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

header("Set-Cookie: cross-site-cookie=whatever; SameSite=Lax; Secure");
?>
<script src="https://accounts.google.com/gsi/client" async></script>
<div id="g_id_onload"
     data-client_id="240437200182-04djpsevljl445a6v5ifujsjakp0t62m.apps.googleusercontent.com"
     data-context="signin"
     data-ux_mode="popup"
     data-login_uri="https://127.0.0.1:8000/api/google-calendar/authenticate"
     data-auto_select="true"
     data-itp_support="true">
</div>

<div class="g_id_signin"
     data-type="standard"
     data-shape="rectangular"
     data-theme="filled_black"
     data-text="signin_with"
     data-size="large"
     data-logo_alignment="left">
</div>

<?php

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
/*
require_once 'C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\vendor\autoload.php';

session_start();

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
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
  echo("Redirecting to Google Calendar API..." . $redirect_uri);
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
*/