<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
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
}
