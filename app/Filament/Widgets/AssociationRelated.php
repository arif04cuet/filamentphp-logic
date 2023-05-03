<?php

namespace App\Filament\Widgets;

use App\Models\Association;
use App\Models\Committee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Location;
use App\Models\CrfGroup;
use App\Models\CrfBeneficiary;
use App\Models\Deposit;
use App\Models\Resolution;
use App\Models\User;
use App\Traits\GeoAwareWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AssociationRelated extends BaseWidget
{
    use GeoAwareWidget, HasWidgetShield;

    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 3;

    protected function getCards(): array
    {
        $locations = collect($this->getLocationIds());
        $associations = $this->getAssociationList(Association::query())->count();
        $deposits = $this->getAssociationRelatedList(Deposit::query())->count();
        $resolutions = $this->getAssociationRelatedList(Resolution::query())->count();
        $committees = $this->getAssociationRelatedList(Committee::query())->count();

        return [
            Card::make('Associations', $associations)->extraAttributes(['style' => 'background-color: #54fd967a'])->url('admin/associations'),
            Card::make('Deposits', $deposits)->extraAttributes(['style' => 'background-color: #54effd7a'])->url('admin/deposits'),
            Card::make('Resolutions', $resolutions)->extraAttributes(['style' => 'background-color: #fdbd5485'])->url('admin/resolutions'),
            Card::make('Committess', $committees)->extraAttributes(['style' => 'background-color: #b0fd544f'])->url('admin/committees'),
        ];
    }
}
