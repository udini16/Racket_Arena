<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // 👇 Relationship: A booking belongs to a Court
    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    // 👇 Relationship: A booking belongs to a User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
