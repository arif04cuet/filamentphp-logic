<?php

namespace App\Filament\Resources\ResolutionResource\Pages;

use App\Filament\Resources\ResolutionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateResolution extends CreateRecord
{
    protected static string $resource = ResolutionResource::class;

    // protected function getRedirectUrl(): string
    // {
    //     return $this->getResource()::getUrl('index');
    // }
}
