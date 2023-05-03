<?php

namespace App\Filament\Resources\InvestmentResource\Pages;

use App\Filament\Resources\InvestmentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvestments extends ListRecords
{
    protected static string $resource = InvestmentResource::class;
    protected static ?string $title = 'Businesses';

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Business'),
        ];
    }
}
