<?php

namespace App\Filament\Resources\CrfBeneficiaryResource\Pages;

use App\Filament\Resources\CrfBeneficiaryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCrfBeneficiaries extends ListRecords
{
    protected static string $resource = CrfBeneficiaryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
