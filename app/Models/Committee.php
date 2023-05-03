<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'association_id', 'validity_from', 'validity_to', 'objective'
    ];

    protected $casts = [
        'validity_from' => 'date',
        'validity_to' => 'date',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(BeneficiaryCommittee::class);
    }
}
