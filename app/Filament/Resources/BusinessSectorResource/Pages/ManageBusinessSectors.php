<?php

namespace App\Filament\Resources\BusinessSectorResource\Pages;

use App\Filament\Resources\BusinessSectorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBusinessSectors extends ManageRecords
{
    protected static string $resource = BusinessSectorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
