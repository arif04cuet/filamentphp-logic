<?php

namespace App\Filament\Resources\ResolutionResource\Pages;

use App\Filament\Resources\ResolutionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResolution extends EditRecord
{
    protected static string $resource = ResolutionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
