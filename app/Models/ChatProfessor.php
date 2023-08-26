<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatProfessor extends Model
{
    use HasFactory;

    protected $table = 'chat_professor';
    protected $fillable = [
        'chat_room_id',
        'professor_id',
    ];

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
