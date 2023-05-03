<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['location_type_id'] == 2)
            $data['parent_id'] = 520;
        elseif ($data['location_type_id'] == 3)
            $data['parent_id'] = $data['division'];
        elseif ($data['location_type_id'] == 4)
            $data['parent_id'] = $data['district'];
        elseif ($data['location_type_id'] == 5)
            $data['parent_id'] = $data['upazila'];
        elseif ($data['location_type_id'] == 6)
            $data['parent_id'] = $data['union'];
        elseif ($data['location_type_id'] == 7)
            $data['parent_id'] = $data['ward'];
        return $data;
    }
}
