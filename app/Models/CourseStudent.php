<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    use HasFactory;

    protected $table = 'course_student';
    protected $fillable = [
        'course_id',
        'student_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /** Filters */
    public function scopeFilterByCourseName($query, $courseName)
    {
        return $query->whereHas('course', function ($query) use ($courseName) {
            return $query->where('name', 'LIKE', '%' . $courseName . '%');
        });
    }

    /** Sorts */
    public function scopeSortByCourseName($query, $sortDirection)
    {
        return $query->join('courses', 'courses.id', '=', 'course_student.course_id')
            ->orderBy('courses.name', $sortDirection)
            ->select('course_student.*');
    }
}
