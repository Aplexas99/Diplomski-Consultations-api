<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProfessor extends Model
{
    use HasFactory;
    
    protected $table = 'course_professor';
    protected $fillable = [
        'course_id',
        'professor_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }
}
