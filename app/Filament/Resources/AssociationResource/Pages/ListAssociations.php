<?php

namespace App\Filament\Resources\AssociationResource\Pages;

use App\Filament\Resources\AssociationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssociations extends ListRecords
{
    protected static string $resource = AssociationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('migrator')->url(route('filament.resources.association-migrators.create'))
        ];
    }
}
