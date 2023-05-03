<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'crf_beneficiary_id',
        'beneficiary_name',
        'father_name_spouse',
        'mother_name',
        'age',
        'occupation',
        'beneficiary_mobile',
        'beneficiary_nid_br',
        'location_id',
        'village_name',
        'association_id',
        'applicant_photo',
        'applicant_signature',
        'nominee_id',
    ];

    protected $casts = [
        'applicant_photo' => 'array',
        'applicant_signature' => 'array'
    ];

    //relation

    public function crfBeneficiary()
    {
        return $this->belongsTo(CrfBeneficiary::class);
    }

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function group()
    {
        return $this->belongsTo(CrfGroup::class, 'crf_group_id', 'group_id');
    }

    public function committeeRole()
    {
        return $this->belongsTo(CommitteeRole::class);
    }

    public function nominee()
    {
        return $this->hasOne(Nominee::class);
    }

    public function investments()
    {
        return $this->morphMany(Investment::class, 'entity');
    }

    public function committees()
    {
        return $this->belongsToMany(BeneficiaryCommittee::class, 'committee_id')->withPivot('committee_role_id');
    }
}
