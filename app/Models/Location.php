<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'user_id',
        'parent_id',
        'location_type_id',
        'logic_location',
    ];

    //scope

    public function scopeType($query, $type)
    {
        return $query->whereHas('locationType', fn ($q) => $q->where('name', $type));
    }

    //relations

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function locationType()
    {
        return $this->belongsTo(LocationType::class);
    }

    public function childRecursiveFlatten($includeParent = false)
    {
        $result = collect();

        if ($includeParent)
            $result->push([
                'id' => $this->id,
                'name' => $this->name
            ]);

        foreach ($this->descendants as $item) {
            $result->push([
                'id' => $item->id,
                'name' => $item->name
            ]);
            $result = $result->merge($item->childRecursiveFlatten());
        }

        return $result;
    }

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    //functions

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function division(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match ($this->location_type_id) {
                2 => $this,
                3 => $this->parent,
                4 => $this->parent->parent,
                5 => $this->parent->parent->parent,
                6 => $this->parent->parent->parent->parent,
                7 => $this->parent->parent->parent->parent->parent,
            },
        );
    }


    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function district(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match ($this->location_type_id) {
                3 => $this,
                4 => $this->parent,
                5 => $this->parent->parent,
                6 => $this->parent->parent->parent,
                7 => $this->parent->parent->parent->parent,
            },
        );
    }


    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function upazila(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match ($this->location_type_id) {
                4 => $this,
                5 => $this->parent,
                6 => $this->parent->parent,
                7 => $this->parent->parent->parent,
            },
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function union(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match ($this->location_type_id) {
                5 => $this,
                6 => $this->parent,
                7 => $this->parent->parent,
            },
        );
    }

    protected function ward(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match ($this->location_type_id) {
                6 => $this,
                7 => $this->parent,
            },
        );
    }

    protected function village(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match ($this->location_type_id) {
                7 => $this,
            },
        );
    }

    public function associations()
    {
        return $this->belongsToMany(Association::class);
    }
}
