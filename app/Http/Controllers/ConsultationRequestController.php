<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConsultationRequestResource;
use App\Models\ConsultationRequest;
use App\Http\Requests\StoreConsultationRequestRequest;
use App\Http\Requests\UpdateConsultationRequestRequest;
use App\Models\Schedule;
use DateTime;
use Illuminate\Http\Request;

class ConsultationRequestController extends Controller
{

    private $googleCalendarController;
    public function __construct()
    {
        $this->googleCalendarController = new GoogleCalendarController();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $consultationRequests = ConsultationRequest::all();
        return ConsultationRequestResource::collection($consultationRequests);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConsultationRequestRequest $request)
    {
        $date = new DateTime($request->date);
        $formattedDate = $date->format('Y-m-d H:i:s');

        $schedule = Schedule::where('professor_id', $request->professor_id)
            ->where('date', $formattedDate)
            ->first();

        if (!$schedule) {
            $date = new DateTime($request->date);
            $formattedDate = $date->format('Y-m-d H:i:s');
            $schedule = Schedule::create([
                'professor_id' => $request->professor_id,
                'date' => $formattedDate,
            ]);
        }
        $consultationRequest = new ConsultationRequest($request->validated());

        $formattedStartTime = new DateTime($request->start_time);
        $formattedEndTime = new DateTime($request->end_time);
        $consultationRequest->start_time = $formattedStartTime->format('Y-m-d H:i:s');
        $consultationRequest->end_time = $formattedEndTime->format('Y-m-d H:i:s');
        $consultationRequest->schedule_id = $schedule->id;

        $consultationRequest->save();

        return new ConsultationRequestResource($consultationRequest);
    }


    /**
     * Display the specified resource.
     */
    public function show(ConsultationRequest $consultationRequest)
    {
        return new ConsultationRequestResource($consultationRequest);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConsultationRequest $consultationRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsultationRequestRequest $request, ConsultationRequest $consultationRequest)
    {
        $consultationRequest->update($request->validated());
        return new ConsultationRequestResource($consultationRequest);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsultationRequest $consultationRequest)
    {
        $consultationRequest->delete();
        return new ConsultationRequestResource($consultationRequest);
    }

    public function getBookedAppointmentsForProfessor(Request $request, string $professorId)
    {
        $date = new DateTime($request->date);
        $formattedDate = $date->format('Y-m-d');

        $schedule = Schedule::where('professor_id', $professorId)
            ->where('date', $formattedDate)
            ->first();

        if (!$schedule) {
            return [
                'data' => [],
            ];
        }
        $consultationRequests = ConsultationRequest::where('professor_id', $professorId)
            ->where('schedule_id', $schedule->id)
            ->whereIn('status', ['PENDING', 'ACCEPTED'])
            ->get();

        return ConsultationRequestResource::collection($consultationRequests);
    }

    public function getScheduledConsultationRequests(Request $request)
    {
        $userId = $request->user()->student->id;
        $consultationRequests = ConsultationRequest::where('student_id', $userId)
            ->whereIn('status', ['ACCEPTED'])
            ->with('professor', 'schedule')
            ->whereHas('schedule', function ($query) {
                $query->where('date', '=', now()->format('Y-m-d'))
                    ->orWhere('date', '>', now()->format('Y-m-d'))
                    ->orderBy('date', 'asc');
            })
            ->get();

        $consultationRequests = $consultationRequests->filter(function ($consultationRequest) {
            $startTime = new DateTime($consultationRequest->start_time);
            if ($consultationRequest->schedule->date == now()->format('Y-m-d'))
                return $startTime->format('H:i') > now()->subMinutes(10)->format('H:i');
            else
                return true;
        });

        return ConsultationRequestResource::collection($consultationRequests);
    }

    public function getPendingConsultationRequests(Request $request)
    {
        $userId = $request->user()->student->id;
        $consultationRequests = ConsultationRequest::where('student_id', $userId)
            ->whereIn('status', ['PENDING'])
            ->with('professor', 'schedule')
            ->whereHas('schedule', function ($query) {
                $query->where('date', '>=', now()->format('Y-m-d'))
                    ->orderBy('date', 'asc');
            })
            ->get();

        $consultationRequests = $consultationRequests->filter(function ($consultationRequest) {
            $startTime = new DateTime($consultationRequest->start_time);
            if ($consultationRequest->schedule->date == now()->format('Y-m-d'))
                return $startTime->format('H:i') > now()->subMinutes(10)->format('H:i');
            else
                return true;
        });
        return ConsultationRequestResource::collection($consultationRequests);
    }

    public function getRejectedConsultationRequests(Request $request){
        $userId = $request->user()->student->id;
        $consultationRequests = ConsultationRequest::where('student_id', $userId)
            ->whereIn('status', ['REJECTED'])
            ->with('professor', 'schedule')
            ->whereHas('schedule', function ($query) {
                $query->where('date', '>=', now()->format('Y-m-d'))
                    ->orderBy('date', 'asc');
            })
            ->get();
        return ConsultationRequestResource::collection($consultationRequests);
    }

    public function getScheduledConsultationRequestsProfessor(Request $request)
    {
        $userId = $request->user()->professor->id;
        $consultationRequests = ConsultationRequest::where('professor_id', $userId)
            ->whereIn('status', ['ACCEPTED'])
            ->with('student', 'schedule', 'professor')
            ->whereHas('schedule', function ($query) {
                $query->where('date', '=', now()->format('Y-m-d'))
                    ->orWhere('date', '>', now()->format('Y-m-d'))
                    ->orderBy('date', 'asc');
            })
            ->get();

        $consultationRequests = $consultationRequests->filter(function ($consultationRequest) {
            $startTime = new DateTime($consultationRequest->start_time);
            if ($consultationRequest->schedule->date == now()->format('Y-m-d'))
                return $startTime->format('H:i') > now()->subMinutes(10)->format('H:i');
            else
                return true;
        });

        return ConsultationRequestResource::collection($consultationRequests);
    }

    public function getPendingConsultationRequestsProfessor(Request $request)
    {
        $userId = $request->user()->professor->id;
        $consultationRequests = ConsultationRequest::where('professor_id', $userId)
            ->whereIn('status', ['PENDING'])
            ->with('student', 'schedule', 'professor')
            ->whereHas('schedule', function ($query) {
                $query->where('date', '>=', now()->format('Y-m-d'))
                    ->orderBy('date', 'asc');
            })
            ->get();

        $consultationRequests = $consultationRequests->filter(function ($consultationRequest) {
            $startTime = new DateTime($consultationRequest->start_time);
            if ($consultationRequest->schedule->date == now()->format('Y-m-d'))
                return $startTime->format('H:i') > now()->subMinutes(10)->format('H:i');
            else
                return true;
        });
        return ConsultationRequestResource::collection($consultationRequests);
    }

    public function getRejectedConsultationRequestsProfessor(Request $request){
        $userId = $request->user()->professor->id;
        $consultationRequests = ConsultationRequest::where('professor_id', $userId)
            ->whereIn('status', ['REJECTED'])
            ->with('student', 'schedule', 'professor')
            ->whereHas('schedule', function ($query) {
                $query->where('date', '>=', now()->format('Y-m-d'))
                    ->orderBy('date', 'asc');
            })
            ->get();
        return ConsultationRequestResource::collection($consultationRequests);
    }

    public function acceptConsultationRequest(Request $request, ConsultationRequest $consultationRequest)
    {
        $event = $this->googleCalendarController->addEvent($request);
        ConsultationRequest::find($request->consultation['_id'])->update([
            'status' => 'accepted',
            'location' => $request->consultation['_location'],
            'link' => $event->hangoutLink,
        ]);
        return response()->json($event, 200);
    }

    public function rejectConsultationRequest(Request $request, ConsultationRequest $consultationRequest)
    {
        $consultationRequest = ConsultationRequest::find($request->consultation['_id'])->update([
            'status' => 'rejected',
            'reason' => $request->consultation['_reason'],
        ]);
        return response()->json($consultationRequest, 200);
    }
}