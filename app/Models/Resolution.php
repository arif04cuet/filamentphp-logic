<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resolution extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'association_id', 'description', 'date_time', 'file'];

    protected $casts = [
        'file' => 'array'
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);
    }
}
