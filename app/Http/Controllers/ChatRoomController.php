<?php

namespace App\Http\Controllers;

use App\Models\ChatProfessor;
use App\Models\ChatRoom;
use App\Http\Requests\StoreChatRoomRequest;
use App\Http\Requests\UpdateChatRoomRequest;
use App\Models\ChatStudent;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = ChatRoom::all();
        return $rooms;
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
    public function store(StoreChatRoomRequest $request)
    {
        $room = ChatRoom::create($request->validated());
        return $room;
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatRoom $chatRoom)
    {
        return $chatRoom;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChatRoomRequest $request, ChatRoom $chatRoom)
    {
        $chatRoom->update($request->validated());
        return $chatRoom;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatRoom $chatRoom)
    {
        $chatRoom->delete();
        return $chatRoom;
    }

    public function addProfessorToChatRoom(Request $request, $chatRoomId, $professorId)
    {
        $existingRecord = ChatProfessor::where([
            'chat_room_id' => $chatRoomId,
            'professor_id' => $professorId,
        ])->first();
    
        if ($existingRecord) {
            return response()->json(['message' => 'Chat room and professor pair already exists'], 409); // Conflict status code
        }
    
       ChatProfessor::create([
            'chat_room_id' => $chatRoomId,
            'professor_id' => $professorId,
        ]);
        return response()->json(['message' => 'Professor added to chat room']);
    }

    public function removeProfessorFromChatRoom(Request $request, $chatRoomId, $professorId)
    {
        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        if(!$chatRoom)
        {
            return response()->json(['message' => 'Chat room not found'], 404);
        }
        $chatRoom->professors()->detach($professorId);

        return response()->json(['message' => 'Professor removed from chat room']);
    }

    public function addStudentToChatRoom(Request $request, $chatRoomId, $studentId)
    {
        $existingRecord = ChatStudent::where([
            'chat_room_id' => $chatRoomId,
            'student_id' => $studentId,
        ])->first();
    
        if ($existingRecord) {
            return response()->json(['message' => 'Chat room and student pair already exists'], 409); // Conflict status code
        }
    
       ChatStudent::create([
            'chat_room_id' => $chatRoomId,
            'student_id' => $studentId,
        ]);
        return response()->json(['message' => 'Student added to chat room']);
    }
    
    public function removeStudentFromChatRoom(Request $request, $chatRoomId, $studentId)
    {
        $chatRoom = ChatRoom::findOrFail($chatRoomId);
        if (!$chatRoom)
        {
            return response()->json(['message' => 'Chat room not found'], 404);
        }
        $chatRoom->students()->detach($studentId);

        return response()->json(['message' => 'Student removed from chat room']);
    }
}