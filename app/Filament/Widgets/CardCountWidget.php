<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Location;
use App\Models\CrfGroup;
use App\Models\CrfBeneficiary;
use App\Models\User;
use App\Traits\GeoAwareWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class CardCountWidget extends BaseWidget
{
    use GeoAwareWidget, HasWidgetShield;

    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 2;

    protected function getCards(): array
    {

        $locations = collect($this->getLocationIds());

        $crf_groups = $this->getList(CrfGroup::query())->count();

        $crf_beneficiaries = $this->getList(CrfBeneficiary::query())->count();

        $users = $this->getList(User::query())->count();

        return [
            Card::make('Cmf Users', $users)->extraAttributes(['style' => 'background-color: #54effd7a'])->url('admin/users'),
            Card::make('Crf Groups', $crf_groups)->extraAttributes(['style' => 'background-color: #54fd967a'])->url('admin/crf-groups'),
            Card::make('Crf Beneficiaries', $crf_beneficiaries)->extraAttributes(['style' => 'background-color: #a7fd544f'])->url('admin/crf-beneficiaries'),
            Card::make('Locations', $locations->count())->extraAttributes(['style' => 'background-color: #fdbd5485'])->url('admin/locations'),
        ];
    }
}
