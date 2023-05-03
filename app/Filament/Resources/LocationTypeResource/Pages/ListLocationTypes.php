<?php

namespace App\Filament\Resources\LocationTypeResource\Pages;

use App\Filament\Resources\LocationTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocationTypes extends ListRecords
{
    protected static string $resource = LocationTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
