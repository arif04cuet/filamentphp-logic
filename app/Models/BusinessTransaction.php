<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'type',
        'title',
        'code_id',
        'transaction_date',
        'amount'
    ];

    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    public static function businessType($key = null)
    {
        $types = [
            self::TYPE_INCOME => 'income',
            self::TYPE_EXPENSE => 'expense',
        ];

        if (!is_null($key) && isset($types[$key]))
            return $types[$key];

        return $types;
    }

    public function business()
    {
        return $this->belongsTo(Investment::class, 'business_id');
    }

    public function codeId()
    {
        return $this->belongsTo(BusinessSector::class, 'code_id');
    }
}
