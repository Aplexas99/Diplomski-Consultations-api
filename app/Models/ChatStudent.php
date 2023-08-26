<?php

namespace App\Models;

use App\Models\ChatRoom;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatStudent extends Model
{
    use HasFactory;
    protected $table = 'chat_student';

    protected $fillable = [
        'chat_room_id',
        'student_id',
    ];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
