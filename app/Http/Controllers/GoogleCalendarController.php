<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
use Google\Service\Calendar\CalendarListEntry;
use Google\Service\Calendar\Event;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_ConferenceSolutionKey;
use Google_Service_Calendar_CreateConferenceRequest;
use Google_Service_Calendar_Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Carbon;

class GoogleCalendarController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig('C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\storage\app\client_secret.json');
        $this->client->addScope(Calendar::CALENDAR);
    }

      /**
     * Gets a google client
     *
     * @return \Google_Client
     * INCOMPLETE
     */
    private function getClient():\Google_Client
    {
        // load our son that contains our credentials for accessing google's api as a json string
        $configJson = 'C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\storage\app\client_secret.json';
        // define an application name
        $applicationName = 'diplomski-rad-397814';

        // create the client
        $client = new \Google_Client();
        $client->setApplicationName($applicationName);
        $client->setAuthConfig($configJson);
        $client->setAccessType('offline'); // necessary for getting the refresh token
        $client->setApprovalPrompt ('force'); // necessary for getting the refresh token
        // scopes determine what google endpoints we can access. keep it simple for now.
        $client->setScopes(
            [
                \Google\Service\Oauth2::USERINFO_PROFILE,
                \Google\Service\Oauth2::USERINFO_EMAIL,
                \Google\Service\Oauth2::OPENID,
                Calendar::CALENDAR_READONLY,
                Calendar::CALENDAR_EVENTS,
            ]
        );
        $client->setIncludeGrantedScopes(true);
        return $client;
    } // getClient

      /**
     * Return the url of the google auth.
     * FE should call this and then direct to this url.
     *
     * INCOMPLETE
     */
    public function getAuthUrl(Request $request)
    {
        /**
         * Create google client
         */
        $client = $this->getClient();

        /**
         * Generate the url at google we redirect to
         */
        $authUrl = $client->createAuthUrl();

        /**
         * HTTP 200
         */
        return response()->json($authUrl, 200);
    } // getAuthUrl

       /**
     * Login and register
     * Gets registration data by calling google Oauth2 service
     *
     */
    public function postLogin(Request $request)
    {

        /**
         * Get authcode from the query string
         * Url decode if necessary
         */
        $authCode = urldecode($request->input('auth_code'));

        /**
         * Google client
         */
        $client = $this->getClient();

        /**
         * Exchange auth code for access token
         * Note: if we set 'access type' to 'force' and our access is 'offline', we get a refresh token. we want that.
         */
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        /**
         * Set the access token with google. nb json
         */
        $client->setAccessToken(json_encode($accessToken));

        /**
         * Get user's data from google
         */
        $service = new \Google\Service\Oauth2($client);
        $userFromGoogle = $service->userinfo->get();

        /**
         * Select user if already exists
         */
        $user = User::where('provider_name', '=', 'google')
            ->where('provider_id', '=', $userFromGoogle->id)
            ->first();

        /**
         */
        if (!$user) {
            $user = User::find(auth()->guard('sanctum')->user()->id);
            $user->provider_id = $userFromGoogle->id;
            $user->provider_name = 'google';
            $user->google_access_token_json = json_encode($accessToken);
            $user->update();
        }
        /**
         * Save new access token for existing user
         */
        else {
            $user->google_access_token_json = json_encode($accessToken);
            $user->save();
        }

        /**
         * Log in and return token
         * HTTP 201
         */
        $token = $user->createToken("Google")->accessToken;
        return response()->json($token, 201);
    } // postLogin

      /**
     * Returns a google client that is logged into the current user
     *
     * @return \Google_Client
     */
    private function getUserClient():\Google_Client
    {
        /**
         * Get Logged in user
         */
        $user = User::where('id', '=', auth()->guard('sanctum')->user()->id)->first();

        /**
         * Strip slashes from the access token json
         * if you don't strip mysql's escaping, everything will seem to work
         * but you will not get a new access token from your refresh token
         */
        $accessTokenJson = stripslashes($user->google_access_token_json);

        /**
         * Get client and set access token
         */
        $client = $this->getClient();
        $client->setAccessToken($accessTokenJson);

        /**
         * Handle refresh
         */
        if ($client->isAccessTokenExpired()) {
            // fetch new access token
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $client->setAccessToken($client->getAccessToken());

            // save new access token
            $user->google_access_token_json = json_encode($client->getAccessToken());
            $user->save();
        }

        return $client;
    } // getUserClient

     /**
     * Get meta data on a page of files in user's google drive
     *
     * @return JsonResponse
     */
    public function getEvents(Request $request):JsonResponse
    {
        /**
         * Get google api client for session user
         */
        $client = $this->getUserClient();

        /**
         * Create a service using the client
         * @see vendor/google/apiclient-services/src/
         */
        $service = new Calendar($client);

        /**
         * Call google api to get a list of files in the drive
         */
        $results = $service->events->listEvents('primary');

        /**
         * HTTP 200
         */
        return response()->json($results, 200);
    }

    /**
 * Add a new event to Google Calendar
 *
 */
public function addEvent(Request $request)
{
    try {
        $client = $this->getUserClient();
        if(!$client){
            return response()->json(['error' => 'User not found'], 404);
        }

        $service = new Calendar($client);
        $calendarId = 'primary';
        
        $formatted_start_hms = Carbon::parse($request->consultation['_startTime'])->format('H:i:s');
        $formatted_end_hms = Carbon::parse($request->consultation['_endTime'])->format('H:i:s');

        $start_time = new \DateTime($request->consultation['_schedule']['_date'] . ' ' . $formatted_start_hms);
        $end_time = new \DateTime($request->consultation['_schedule']['_date'] . ' ' . $formatted_end_hms);
         
        $formatted_start_time = $start_time->format('Y-m-d\TH:i:sP');
        $formatted_end_time = $end_time->format('Y-m-d\TH:i:sP');

        $event = new Google_Service_Calendar_Event([
            'summary' => $request->title,
            'location' => $request->consultation['_location'],
            'start' => [
                'dateTime' => $formatted_start_time,
                'timeZone' => 'Europe/Zagreb',
            ],
            'end' => [
                'dateTime' => $formatted_end_time,
                'timeZone' => 'Europe/Zagreb',
            ],
            'attendees' => [
                ['email' => $request->consultation['_student']['_user']['_email']],
            ],
            ]);

        if($request->consultation['_type'] == 'ONLINE'){
            $solution_key = new Google_Service_Calendar_ConferenceSolutionKey();
            $solution_key->setType("hangoutsMeet");
            $confrequest = new Google_Service_Calendar_CreateConferenceRequest();
            $confrequest->setRequestId("Konzultacije");
            $confrequest->setConferenceSolutionKey($solution_key);
            $confdata = new Google_Service_Calendar_ConferenceData();
            $confdata->setCreateRequest($confrequest);
            $event->setConferenceData($confdata);
            $createdEvent = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);
        }
        else{
            $createdEvent = $service->events->insert($calendarId, $event);
        }

        return $createdEvent;
    } catch (\Exception $e) {
        \Log::error('Failed to add event: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
