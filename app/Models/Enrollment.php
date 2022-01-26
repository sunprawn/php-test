<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'enrollments';

    public function students()
    {
        return $this->belongsTo('App\Models\Student', 'student_id');
    }

    public function courses()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }
}
