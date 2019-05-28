<?php

namespace App;

class Payment extends BaseModel
{
    protected $fillable = [
        'amount',
        'student_id',
        'type',
        'payed_at',
        'invoiced'
    ];

    protected $casts = [
        'invoiced' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo('App\Student');
    }
}
