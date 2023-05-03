<?php

namespace App\Filament\Resources\CommitteeRoleResource\Pages;

use App\Filament\Resources\CommitteeRoleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\BeneficiaryCommittee;

class EditCommitteeRole extends EditRecord
{
    protected static string $resource = CommitteeRoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    BeneficiaryCommittee::where('committee_role_id', $this->record->id)->delete();
                }),
        ];
    }
}
