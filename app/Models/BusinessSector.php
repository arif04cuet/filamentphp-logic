<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSector extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'title', 'code_id', 'parent_id'];

    const TYPE_INCOME = 'income';
    const TYPE_EXPENSE = 'expense';

    public static function businessSectorType($key = null)
    {
        $types = [
            self::TYPE_INCOME => 'income',
            self::TYPE_EXPENSE => 'expense',
        ];

        if (!is_null($key) && isset($types[$key]))
            return $types[$key];

        return $types;
    }

    public function parent()
    {
        return $this->belongsTo(BusinessSector::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BusinessSector::class, 'parent_id');
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }
}
