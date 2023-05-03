<?php

namespace App\Filament\Resources\AssociationMigratorResource\Pages;

use App\Filament\Resources\AssociationMigratorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssociationMigrator extends EditRecord
{
    protected static string $resource = AssociationMigratorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
