<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Calendar;

class GoogleAuth extends Controller
{

    public function index()
    {
        require_once 'C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\vendor\autoload.php';

        session_start();
        dd("index");

        $client = new Client();
        $client->setAuthConfig('C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\storage\app\client_secret.json');
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');
        $client->addScope(Calendar::CALENDAR);

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            echo ("Access token is set");
            $client->setAccessToken($_SESSION['access_token']);
            $calendar = new Calendar($client);
            $events = $calendar->events->listEvents('primary');
            return $_SESSION['access_token'];
        } else {
            var_dump("Access token is not set");
            if (!isset($_GET['code'])) {
                $auth_url = $client->createAuthUrl();
                header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
            } else {
                $client->authenticate($_GET['code']);
                $_SESSION['access_token'] = $client->getAccessToken();
            }
        }

        return "sddsadsdsa";
    }
}