<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryCommittee extends Model
{
    use HasFactory;

    protected $fillable = [
        'beneficiary_id', 'committee_id', 'committee_role_id'
    ];

    protected $table = 'beneficiary_committee';

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function committeeRole()
    {
        return $this->belongsTo(CommitteeRole::class);
    }
}
