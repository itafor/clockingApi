<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clock extends Model
{
    protected $fillable = [
        'timeIn','timeOut', 'status','studentId',
   ];
}
