<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'display_id',
        'company_scope_id',
        'name',
        'mobile',
        'city',
        'state',
        'pin_code',
        'date_of_joining',
        'role_id',
        'created_by_email',
        'status',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }
}
