<?php

namespace App\Filament\Resources\ResolutionResource\Pages;

use App\Filament\Resources\ResolutionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResolutions extends ListRecords
{
    protected static string $resource = ResolutionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
