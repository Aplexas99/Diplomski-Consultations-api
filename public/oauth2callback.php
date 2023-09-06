<?php
require_once 'C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\vendor\autoload.php';

session_start();

$client = new Google\Client();
$client->setAuthConfigFile('C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\storage\app\client_secret.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');
$client->addScope(Google\Service\Calendar::CALENDAR_READONLY);
echo("Oauth2callback");

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/tt';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}