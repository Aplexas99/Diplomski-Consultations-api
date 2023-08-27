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
