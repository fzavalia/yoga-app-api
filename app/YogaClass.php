<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YogaClass extends Model
{
    protected $hidden = [
        'pivot',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'date',
        'user_id'
    ];

    public function students()
    {
        return $this->belongsToMany('App\Student');
    }

    public function syncStudentsIfArrayContainsStudentIds(array $arrayWithStudentIds)
    {
        if (array_key_exists('student_ids', $arrayWithStudentIds)) {
            $this->students()->sync($arrayWithStudentIds['student_ids']);
        }
    }
}
