<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Association extends Model
{
    use HasFactory;

    const DEPOSIT_A = 1;
    const DEPOSIT_B = 2;
    const SHARE_A = 1;
    const SHARE_B = 2;

    protected $fillable = [
        'name', 'deposit_type', 'share_type', 'beneficiary_no'
    ];

    public static function depositType($key = null)
    {
        $deposits = [
            self::DEPOSIT_A => 'সীমাবদ্ধ',
            self::DEPOSIT_B => 'অসীমাবদ্ধ',
        ];

        if (!is_null($key) && isset($deposits[$key]))
            return $deposits[$key];

        return $deposits;
    }

    public static function shareType($key = null)
    {
        $shares = [
            self::SHARE_A => 'শেয়ার ভিত্তিক',
            self::SHARE_B => 'শেয়ার বর্হিভূতভাবে',
        ];

        if (!is_null($key) && isset($shares[$key]))
            return $shares[$key];

        return $shares;
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class);
    }

    public function resolutions()
    {
        return $this->hasMany(Resolution::class);
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function investments()
    {
        return $this->morphMany(Investment::class, 'entity');
    }

    public function committees()
    {
        return $this->hasMany(Committee::class);
    }
}
