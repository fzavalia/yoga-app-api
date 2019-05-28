<?php

namespace App;

class Student extends BaseModel
{
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'dni'
    ];

    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    public function yogaClasses() 
    {
        return $this->belongsToMany('App\YogaClass');
    }
}
