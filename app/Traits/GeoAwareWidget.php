<?php

namespace App\Traits;

use App\Models\Location;
use App\Models\Association;

trait GeoAwareWidget
{
    public $geo_type;
    public $geo_id;

    protected $locationIds = [];

    public function filteredGeo($data)
    {
        $this->geo_type = $data['type'];
        $this->geo_id = $data['id'];
    }

    public function getListeners()
    {
        return $this->listeners + [
            'filteredGeo'
        ];
    }

    public function getLocationIds()
    {
        $locations = Location::query()->select('id', 'name');

        if ($location_id = $this->geo_id)
            $locations = Location::find($location_id)->childRecursiveFlatten(true);

        $locationIds = $locations->pluck('id', 'id')->toArray();

        $this->locationIds = $locationIds;

        return $locationIds;
    }
    public function getList($query)
    {
        $locationIds = $this->locationIds;

        if (!$locationIds)
            return $query;

        return $query->whereHas('location', function ($q) use ($locationIds) {
            $q->whereIn('id', $locationIds);
        });
    }
    public function getAssociationList($query)
    {
        $locationIds = $this->locationIds;

        if (!$locationIds) {
            return $query;
        }

        return $query->whereHas('locations', function ($q) use ($locationIds) {
            $q->whereIn('locations.id', $locationIds);
        });
    }
    public function getAssociationRelatedList($query)
    {
        $locationIds = $this->locationIds;

        if (!$locationIds)
            return $query;

        $associationQuery = Association::query()
            ->whereIn('id', function ($query) use ($locationIds) {
                $query->select('association_id')
                    ->from('association_location')
                    ->whereIn('location_id', $locationIds);
            });

        return $query->whereIn('association_id', $associationQuery->pluck('id'));
    }
}
