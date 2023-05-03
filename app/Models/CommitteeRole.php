<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteeRole extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }
}
