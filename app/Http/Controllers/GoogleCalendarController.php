<?php

namespace App\Http\Controllers;

use Google\Service\Calendar\CalendarListEntry;
use Illuminate\Http\Request;
use Google\Client;
use Google\Service\Calendar;

class GoogleCalendarController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig('C:\Users\Fujitsu\Desktop\Programiranje\Diplomski\Backend\diplomski-api\storage\app\client_secret.json');
        $this->client->addScope(Calendar::CALENDAR);
    }

    public function authenticate()
    {
        $authUrl = $this->client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {
        if ($request->has('code')) {
            $this->client->authenticate($request->code);
            $accessToken = $this->client->getAccessToken();

            // Store the access token in the session or database for later use
            session(['access_token' => $accessToken]);

            return redirect()->route('google-calendar.events');
        }

        return 'Authentication failed';
    }
    public function getEvents()
    {
        $accessToken = session('access_token');
    
        if (!$accessToken) {
            return response()->json(['message' => 'Access token not found', 'error' => true], 401);
        }
    
        $this->client->setAccessToken($accessToken);
        $calendar = new Calendar($this->client);
    
        try {
            // Get the list of calendars for the authenticated user
            $calendarListResponse = $calendar->calendarList->listCalendarList();
    
            if ($calendarListResponse && count($calendarListResponse) > 0) {
                $calendars = $calendarListResponse->getItems();
                
                $calendarLists = [];
                
                foreach ($calendars as $item) {
                    $calendarLists[] = [
                        'id' => isset($item['id']) ? (string)$item['id'] : '',
                        'summary' => isset($item['summary']) ? (string)$item['summary'] : ''
                    ];
                }
                
                return response()->json(['data' => ['calendars' => $calendarLists]]);
            } else {
                return response()->json(['message' => 'No calendars found', 'error' => false], 200);
            }
        } catch (\Exception | \Throwable $e) {
            // Log the exception for debugging purposes
            \Log::error('Failed to retrieve calendar list: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to retrieve calendar list',
                'error' => true,
            ], 500);
        }
    }
    
}
