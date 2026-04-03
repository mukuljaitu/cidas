<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_date',
        'salesman',
        'salesman_id',
        'party',
        'party_id',
        'bill_type',
        'bill_date',
        'bill_no',
        'transport',
        'transport_id',
        'status',
        'type',
        'receiving_image_path',
        'is_deleted',
    ];

    protected $casts = [
        'order_date' => 'date',
        'bill_date' => 'date',
        'receiving_image_path' => 'array',
        'is_deleted' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function salesmanEmployee()
    {
        return $this->belongsTo(Employee::class, 'salesman_id');
    }

    public function partyRef()
    {
        return $this->belongsTo(Party::class, 'party_id');
    }

    public function transportRef()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }
}
