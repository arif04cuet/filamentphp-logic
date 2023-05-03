<?php

namespace App\Filament\Resources\AssociationMigratorResource\Pages;

use App\Filament\Resources\AssociationMigratorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AssociationResource;
use App\Models\Association;
use App\Models\Beneficiary;
use App\Models\CrfBeneficiary;
use App\Models\CrfGroup;
use App\Models\Location;
use Illuminate\Support\Facades\DB;
use Filament\Pages\Actions\CreateAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateAssociationMigrator extends CreateRecord
{
    protected static string $resource = AssociationMigratorResource::class;
    protected static ?string $title = 'Migrate Association For Groups';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('back')->url(route('filament.resources.associations.index'))->extraAttributes(['style' => 'background-color: #ffffffe3; color: black'])
        ];
    }

    protected function getFormActions(): array
    {
        return array_merge(
            // [$this->getCreateFormAction()->label('Save')->icon('heroicon-o-plus-circle')->size('sm')],
            // [$this->getCancelFormAction()->label('Back')->icon('heroicon-o-arrow-left')->size('sm')],
        );
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
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
            foreach ($this->data['ward_name'] as $ward) {
                $ward = Location::where('parent_id', auth()->user()->location_id)->where('name', $ward)->first();
                $this->data['location_id'][] = $ward->id; // selected ward location ids
            }

            foreach ($this->data['beneficiaries'] as $beneficiary) {
                $crf_benef_info = CrfBeneficiary::where('id', $beneficiary)->first(); // selected beneficiaries

                // new entry in beneficiary
                $beneficiary_data = collect($crf_benef_info);
                $beneficiary_data->forget(['id', 'is_used', 'created_at', 'updated_at', 'beneficiary_account_no', 'district', 'division', 'hh', 'hh_head_mobile_no', 'hh_head_nid_no_br', 'hh_head_name', 'mobile_number_type', 'new_hh_id', 'round', 'union', 'upazila', 'ward', '_id', 'crf_group_id']);
                $beneficiary_data->put('crf_beneficiary_id', $crf_benef_info->id)->put('association_id', $association_id)->put('created_at', \Carbon\Carbon::now())->put('updated_at', \Carbon\Carbon::now());
                Beneficiary::insert($beneficiary_data->toArray());

                $all_group_ids[] = $crf_benef_info->group->id;
            }
            $this->data['group_ids'] = array_unique($all_group_ids); // selected group ids

            // association_location insertion
            foreach ($this->data['location_id'] as $location_id)
                DB::table('association_location')->insert(['association_id' => $association_id, 'location_id' => $location_id]);
            foreach ($this->data['group_ids'] as $group_id)
                CrfGroup::where('id', $group_id)->update(['is_used' => 1]); // is_used flag for groups
            foreach ($this->data['beneficiaries'] as $beneficiary)
                CrfBeneficiary::where('id', $beneficiary)->update(['is_used' => 1]); // is_used flag for crf_beneficiaries

            DB::commit();

            Notification::make()
                ->title('Saved successfully')
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
        return AssociationResource::getUrl('edit', ['record' => $this->record->id]);
    }
}
