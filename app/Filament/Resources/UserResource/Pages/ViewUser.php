<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use XliteDev\FilamentImpersonate\Pages\Actions\ImpersonateAction;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getActions(): array
    {
        return [
            ImpersonateAction::make()->record($this->getRecord())->extraAttributes(['style' => 'background-color: #000000e0']),
            Actions\Action::make('back')->color('success')->url(fn () => route('filament.resources.users.index', ['id' => $this->record->id]))
        ];
    }
}
