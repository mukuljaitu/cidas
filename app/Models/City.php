<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'employee_id',
        'city',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
