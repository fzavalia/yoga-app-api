<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'amount',
        'student_id',
        'type'  
    ];

    public function student() {
        return $this->belongsTo('App\Student');
    }
}
