<?php

namespace App\Models;

use App\Models\ConsultationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'jmbag'];

    /** Filters */
    public function scopeFilterByName($query, $name)
    {
        return $query->where('users.name', 'like', '%' . $name . '%');
    }
    public function scopeFilterByJmbag($query, $jmbag)
    {
        return $query->where('jmbag', 'like', '%' . $jmbag . '%');
    }

    /** Sorts */
    public function scopeSortByName($query, $order = 'asc')
    {
        return $query->select('students.*', 'users.name')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderBy('users.name', $order);
    }
    public function scopeSortByJmbag($query, $order = 'asc')
    {
        return $query->orderBy('jmbag', $order);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id');
    }
    public function consultationRequests()
    {
        return $this->hasMany(ConsultationRequest::class);
    }

}
