<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'product_id',
        'display_id',
        'name',
        'sku',
        'unit',
        'size',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

