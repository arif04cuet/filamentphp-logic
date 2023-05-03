<?php

namespace App\Filament\Resources\BusinessTypeResource\Pages;

use App\Filament\Resources\BusinessTypeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBusinessTypes extends ManageRecords
{
    protected static string $resource = BusinessTypeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
