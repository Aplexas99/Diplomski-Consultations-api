<?php

namespace App\Http\Controllers;

use App\Models\ConsultationRequest;
use App\Http\Requests\StoreConsultationRequestRequest;
use App\Http\Requests\UpdateConsultationRequestRequest;

class ConsultationRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $consultationRequests = ConsultationRequest::all();
        return $consultationRequests;
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
        $consultationRequest = ConsultationRequest::create($request->validated());
        return $consultationRequest;
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsultationRequest $consultationRequest)
    {
        return $consultationRequest;
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
        return $consultationRequest;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsultationRequest $consultationRequest)
    {
        $consultationRequest->delete();
        return $consultationRequest;
    }
}
