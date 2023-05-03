<?php

namespace App\Filament\Resources\CrfGroupResource\Pages;

use App\Filament\Resources\CrfGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrfGroup extends EditRecord
{
    protected static string $resource = CrfGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
