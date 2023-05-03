<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    use HasFactory;

    protected $fillable = ['nominee_name', 'location_id', 'village_id', 'beneficiary_id', 'relation_with_beneficary'];

    public function beneficiary()
    {
        return $this->hasOne(Beneficiary::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
