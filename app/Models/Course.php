<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'course_professor', 'course_id', 'professor_id');
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id');
    }

    /** Sorts */
    public function scopeSortByName($query, $order = 'asc')
    {
        return $query->orderBy('name', $order);
    }

    /** Filters */
    public function scopeFilterByName($query, $name)
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }
}
