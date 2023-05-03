<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrfGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'location_id',
        'ward_0_value',
        'ward_0_label',
        'ward_1_value',
        'ward_1_label',
        'district',
        'upazila',
        'union',
        'crf_round',
        'group_name',
        'group_address',
        'male_beneficiaries',
        'female_beneficiaries',
        'crf_beneficiaries',
        'livelihood_started',
        'group_account_name',
        'group_account_number',
        'bank_name',
        'routing_number',
        'bank_branch_address',
        'money_received',
        'money_invested',
        'remarks',
        'bank_branch_name'
    ];

    //relation

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function members()
    {
        return $this->hasMany(CrfBeneficiary::class, 'crf_group_id', 'group_id');
    }
}
