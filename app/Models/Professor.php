<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;
    
    protected $table = 'professors';
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_professor', 'professor_id', 'course_id');
    }
    public function consultationSchedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_professor', 'professor_id', 'chat_room_id');
    }

        /** Sorts */
        public function scopeSortByName($query, $order = 'asc')
        {
            return $query->select('professors.*', 'users.name')
            ->join('users', 'professors.user_id', '=', 'users.id')
            ->orderBy('users.name', $order);
        }
        public function scopeSortByLastName($query, $order = 'asc')
        {
            return $query->orderBy('last_name', $order);
        }
        public function scopeSortByEmail($query, $order = 'asc')
        {
            return $query->orderBy('email', $order);
        }
    
        /** Filters */
        public function scopeFilterByName($query, $name)
        {
            return $query->where('users.name', 'like', '%' . $name . '%');
        }
        
        public function scopeFilterByLastName($query, $last_name)
        {
            return $query->where('last_name', 'like', '%' . $last_name . '%');
        }
        public function scopeFilterByEmail($query, $email)
        {
            return $query->where('email', 'like', '%' . $email . '%');
        }
        
}
