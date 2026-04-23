<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    protected $fillable = [
        'display_id',
        'company_code',
        'company_scope_id',
        'name',
        'alias',
        'mobile',
        'gst_no',
        'street_address',
        'city',
        'district',
        'state',
        'pin_code',
        'bank_name',
        'bank_account_no',
        'bank_ifsc',
        'employee_id',
        'created_by_email',
        'status',
        'is_verified',
        'party_type',
        'pan_no',
        'aadhar_card',
        'owner_name',
        'pest_lic',
        'fert_lic',
        'seed_lic',
        'cq1',
        'cq2',
        'stamp',
        'sign',
        'pic',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
