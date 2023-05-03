<?php

namespace App\Filament\Resources\AssociationResource\Pages;

use App\Filament\Resources\AssociationResource;
use App\Models\Association;
use App\Models\Beneficiary;
use App\Models\CrfBeneficiary;
use App\Models\CrfGroup;
use App\Models\Location;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Filament\Pages\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateAssociation extends CreateRecord
{
    protected static string $resource = AssociationResource::class;

    protected function getFormActions(): array
    {
        return array_merge(
            [$this->getCreateFormAction()->label('Save')],
            [$this->getCancelFormAction()->label('Back')],
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $association_id = $this->record->id;

        DB::beginTransaction();
        try {
            foreach ($this->data['location_id'] as $location_id)
                DB::table('association_location')->insert(['association_id' => $association_id, 'location_id' => $location_id]);

            DB::commit();

            Notification::make()
                ->title('Association Saved Successfully')
                ->body('Now you can add beneficiaries')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollback();

            Association::where('id', $association_id)->delete();

            Notification::make()
                ->title('Something went wrong')
                ->danger()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record->id]);
    }
}
