<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role_id',
        'provider_id',
        'provider_name',
        'google_access_token_json',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    /** Relations */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function professor()
    {
        return $this->hasOne(Professor::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /** Sorts */
    public function scopeSortByName($query, $order = 'asc')
    {
        return $query->orderBy('name', $order);
    }
    public function scopeSortByLastName($query, $order = 'asc')
    {
        return $query->orderBy('last_name', $order);
    }
    public function scopeSortByEmail($query, $order = 'asc')
    {
        return $query->orderBy('email', $order);
    }
    public function scopeSortByRole($query, $order = 'asc')
    {
        return $query->join('roles', 'users.role_id', '=', 'roles.id')
        ->orderBy('roles.name', $order);
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
    public function scopeFilterByRole($query, $roleName)
    {
        return $query->select('users.*', 'roles.name AS role_name')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'like', '%' . $roleName . '%');
    }

    public function isAdmin()
    {
        return $this->role->name == 'Admin';
    }
    
    public function isProfessor()
    {
        return $this->role->name == 'Professor';
    }

    public function isStudent()
    {
        return $this->role->name == 'Student';
    }

}