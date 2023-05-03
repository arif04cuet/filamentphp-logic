<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'association_id', 'amount', 'deposit_date', 'reference_id', 'beneficiary_id', 'file'
    ];

    protected $casts = [
        'deposit_date' => 'date',
        'file' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
