<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    // Define exactly what can be filled.
    // Notice 'price' and 'type' are here.
    protected $fillable = [
        'name',
        'type',
        'price',
        'is_active'
    ];

    // Ensure price is always treated as a number
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}