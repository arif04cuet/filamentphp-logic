<?php

namespace App\Filament\Resources\CommitteeRoleResource\Pages;

use App\Filament\Resources\CommitteeRoleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommitteeRoles extends ListRecords
{
    protected static string $resource = CommitteeRoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
