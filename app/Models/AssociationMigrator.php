<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use App\Models\Association;

class AssociationMigrator extends Association
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'associations';
}
