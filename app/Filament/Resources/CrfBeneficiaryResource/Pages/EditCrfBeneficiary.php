<?php

namespace App\Filament\Resources\CrfBeneficiaryResource\Pages;

use App\Filament\Resources\CrfBeneficiaryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrfBeneficiary extends EditRecord
{
    protected static string $resource = CrfBeneficiaryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
