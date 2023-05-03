<?php

namespace App\Filament\Resources\AssociationResource\Pages;

use App\Filament\Resources\AssociationResource;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Association;

class ViewAssociation extends ViewRecord
{
    protected static string $resource = AssociationResource::class;

    protected function getActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $assoc = Association::find($this->record->id);
        $data['association_title'][] = $assoc->name;
        foreach ($assoc->locations as $location)
            $data['selected_ward(s)'][] = $location->name;
        return $data;
    }
}
