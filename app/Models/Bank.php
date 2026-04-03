<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'transaction_date',
        'state',
        'employee_id',
        'party_id',
        'station',
        'issuing_bank',
        'ifsc_code',
        'reference_no',
        'amount',
        'receiving_bank',
        'clear_date',
        'comments',
        'image_paths',
        'status',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'clear_date' => 'date',
        'image_paths' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }
}
