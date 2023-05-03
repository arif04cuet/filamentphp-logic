<?php

namespace App\Filament\Resources\CommitteeResource\Pages;

use App\Filament\Resources\CommitteeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommittees extends ListRecords
{
    protected static string $resource = CommitteeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
