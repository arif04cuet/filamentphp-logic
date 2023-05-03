<?php

namespace App\Filament\Resources\CommitteeResource\Pages;

use App\Filament\Resources\CommitteeResource;
use App\Models\BeneficiaryCommittee;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommittee extends EditRecord
{
    protected static string $resource = CommitteeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    BeneficiaryCommittee::where('committee_id', $this->record->id)->delete();
                }),
        ];
    }
}
