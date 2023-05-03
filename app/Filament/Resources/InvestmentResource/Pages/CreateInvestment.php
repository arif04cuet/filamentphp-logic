<?php

namespace App\Filament\Resources\InvestmentResource\Pages;

use App\Filament\Resources\InvestmentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Association;
use App\Models\Investment;

class CreateInvestment extends CreateRecord
{
    protected static string $resource = InvestmentResource::class;
    protected static ?string $title = 'Create Business';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['entity_type'] == 'App\Models\Association')
            $data['investment_type'] = 1;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
