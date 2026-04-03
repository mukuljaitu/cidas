<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'display_id',
        'name',
        'type',
        'description',
        'created_by',
    ];

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }
}
