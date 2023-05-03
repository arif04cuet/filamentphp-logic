<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrfBeneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_account_no',
        'beneficiary_mobile',
        'beneficiary_nid_br',
        'beneficiary_name',
        'district',
        'division',
        'father_name_spouse',
        'hh',
        'hh_head_mobile_no',
        'hh_head_nid_no_br',
        'hh_head_name',
        'mobile_number_type',
        'new_hh_id',
        'round',
        'union',
        'upazila',
        'village_name',
        'ward',
        '_id',
        'crf_group_id',
        'location_id'
    ];

    //relation

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function group()
    {
        return $this->belongsTo(CrfGroup::class, 'crf_group_id', 'group_id');
    }
}
