<?php

namespace App\Filament\Resources\AssociationResource\Pages;

use App\Filament\Resources\AssociationResource;
use App\Models\Beneficiary;
use App\Models\CrfBeneficiary;
use App\Models\CrfGroup;
use App\Models\Nominee;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\DeleteAction;

class EditAssociation extends EditRecord
{
    protected static string $resource = AssociationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Association')
                ->modalSubheading('Are you sure you\'d like to delete these assocition? Beneficiaries under this association will also be deleted.')
                ->modalButton('Yes, delete them')
                ->before(function (DeleteAction $action) {
                    $beneficiaries = $this->record->beneficiaries;
                    DB::beginTransaction();
                    try {
                        foreach ($beneficiaries as $record) {
                            if ($record->crfBeneficiary) {
                                if ($record->crfBeneficiary->group)
                                    CrfGroup::where('id', $record->crfBeneficiary->group->id)->update(['is_used' => null]);
                                if ($record->nominee)
                                    Nominee::where('id', $record->nominee->id)->delete();
                                CrfBeneficiary::where('id', $record->crfBeneficiary->id)->update(['is_used' => null]);
                            } else {
                                if ($record->nominee)
                                    Nominee::where('id', $record->nominee->id)->delete();
                            }
                            Beneficiary::where('id', $record->id)->delete();
                        }
                        DB::table('association_location')->where('association_id', '=', $this->record->id)->delete();
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollback();
                        $action->cancel();
                    }
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $association_id = $this->record->id;

        DB::beginTransaction();
        try {
            DB::table('association_location')->where('association_id', $association_id)->delete();

            foreach ($this->data['location_id'] as $location_id)
                DB::table('association_location')->insert(['association_id' => $association_id, 'location_id' => $location_id]);

            DB::commit();

            Notification::make()
                ->title('Association Updated Successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollback();

            Notification::make()
                ->title('Something went wrong')
                ->danger()
                ->send();
        }
    }
}
