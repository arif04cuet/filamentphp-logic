<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $table = 'businesses';

    protected $fillable = [
        'title', 
        'entity_id', 
        'entity_type', 
        'investment_type', 
        'location_id', 
        'amount', 
        'from_date', // Business Start Date 
        'to_date', // Tentative Return Date
        'status', 
        'business_type_id', 
        'income_sector_ids', 
        'expense_sector_ids'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'status' => 'boolean',
        'income_sector_ids' => 'array',
        'expense_sector_ids' => 'array',
    ];

    const INVESTMENT_1 = 1;
    const INVESTMENT_2 = 2;
    const INVESTMENT_3 = 3;

    public static function investmentType($key = null)
    {
        $investments = [
            self::INVESTMENT_1 => 'Association Wise',
            self::INVESTMENT_2 => 'Group of Beneficiaries',
            self::INVESTMENT_3 => 'Single Beneficiary',
        ];

        if (!is_null($key) && isset($investments[$key]))
            return $investments[$key];

        return $investments;
    }

    public function entity()
    {
        return $this->morphTo();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function businessTransactions()
    {
        return $this->hasMany(BusinessTransaction::class, 'business_id');
    }

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function incomeSectorIds()
    {
        return $this->belongsTo(BusinessSector::class, 'income_sector_ids');
    }

    public function expenseSectorIds()
    {
        return $this->belongsTo(BusinessSector::class, 'expense_sector_ids');
    }
}
