<?php

namespace App\Traits;
use App\Traits\GeoAwareWidget;

trait DependantDepositWidget
{
    use GeoAwareWidget;

    public $association_id;

    public function depositWidget($data)
    {
        $this->association_id = $data['id'];
    }

    public function getAssociationId()
    {
        return $this->association_id;
    }

    public function getListeners()
    {
        return [
            'depositWidget',
            'filteredGeo'
        ];
    }
}
