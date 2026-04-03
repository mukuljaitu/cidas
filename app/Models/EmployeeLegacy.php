<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLegacy extends Model
{
    use HasFactory;

    protected $table = 'employees_legacy';

    protected $fillable = [
        'Name',
        'supervisor',
        'state',
        'cities',
        'parties',
    ];
}
