<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
    public function consultationSchedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_professor', 'professor_id', 'chat_room_id');
    }
}
