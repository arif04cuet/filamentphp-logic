<?php

namespace App\Filament\Resources\AssociationMigratorResource\Pages;

use App\Filament\Resources\AssociationMigratorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssociationMigrators extends ListRecords
{
    protected static string $resource = AssociationMigratorResource::class;

    protected function getActions(): array
    {
        return [];
    }
}
