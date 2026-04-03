<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
        'display_id',
        'company_scope_id',
        'name',
        'vehicle',
        'vehicle_number',
        'contact',
        'last_trip',
        'total_trips',
        'date_of_joining',
        'created_by_email',
    ];

    protected $casts = [
        'last_trip' => 'date',
        'date_of_joining' => 'date',
        'total_trips' => 'integer',
    ];
}
