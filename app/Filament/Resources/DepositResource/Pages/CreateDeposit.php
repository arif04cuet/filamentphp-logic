<?php

namespace App\Filament\Resources\DepositResource\Pages;

use App\Filament\Resources\DepositResource;
use App\Models\Beneficiary;
use App\Models\Deposit;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['beneficiary_option'] == 'all') {
            $beneficiaries = Beneficiary::where('association_id', $data['association_id'])->get();
            $data['count'] = count($beneficiaries);
            if ($data['count'] > 0) {
                $new_amount = $data['amount'] / $data['count'];
                foreach ($beneficiaries as $i => $beneficiary) {
                    $deposit = new Deposit;
                    $deposit->name = $data['name'];
                    $deposit->association_id = $data['association_id'];
                    $deposit->deposit_date = $data['deposit_date'];
                    $deposit->reference_id = $data['reference_id'];
                    $deposit->file = $data['file'];
                    $deposit->beneficiary_id = $beneficiary->id;
                    $deposit->amount = $new_amount;
                    if ($i == $data['count'] - 1) {
                        $data['beneficiary_id'] = $beneficiary->id;
                        $data['amount'] = $new_amount;
                        return $data;
                    }
                    $deposit->save();
                }
            }
            Notification::make()->title('No Beneficiaries In This Association')->danger()->send();
        }
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        if ($this->data['beneficiary_count'] == 0)
            Deposit::where('id', $this->record->id)->delete();
        else
            Notification::make()->title('Saved')->success()->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
