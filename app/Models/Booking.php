<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'court_id',
        'start_time',
        'end_time',
        'status',
        'total_price',
    ];
}
