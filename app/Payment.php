<?php

namespace App;

class Payment extends BaseModel
{
    protected $fillable = [
        'amount',
        'student_id',
        'type',
        'payed_at',
        'invoiced',
        'user_id'
    ];

    protected $casts = [
        'invoiced' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo('App\Student');
    }
}
