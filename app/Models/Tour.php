<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'state',
        'tour_date',
        'is_supervisor',
        'cities',
        'status',
    ];

    protected $casts = [
        'tour_date' => 'date',
        'is_supervisor' => 'boolean',
    ];
}
