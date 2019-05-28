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
        'date'
    ];

    public function students()
    {
        return $this->belongsToMany('App\Student');
    }
}
