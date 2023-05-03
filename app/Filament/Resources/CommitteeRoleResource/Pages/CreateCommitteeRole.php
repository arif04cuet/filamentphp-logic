<?php

namespace App\Filament\Resources\CommitteeRoleResource\Pages;

use App\Filament\Resources\CommitteeRoleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCommitteeRole extends CreateRecord
{
    protected static string $resource = CommitteeRoleResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return static::getResource()::getUrl('index');
    // }
}
