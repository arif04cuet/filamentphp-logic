<?php

namespace App\Filament\Widgets;

use App\Models\Association;
use App\Models\Beneficiary;
use App\Models\Deposit;
use App\Models\Location;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Tables\Filters\Filter;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;
use App\Traits\DependantDepositWidget;

class AssociationWiseDeposit extends BaseWidget
{
    use DependantDepositWidget, HasWidgetShield;

    protected static ?int $sort = 3;

    protected function getTableQuery(): Builder
    {
        $locations = collect($this->getLocationIds());
        $query = Association::query();

        $associationId = $this->getTableFilterState('filters')['association'] ?? null;
        if ($associationId) {
            $query->where('id', $associationId);
        }

        $superAdmin = auth()->user()->roles->contains('id', 1);
        if (!$superAdmin) {
            $association_ids = Location::with('associations')->where('parent_id', auth()->user()->location_id)->type('Ward')->get()
                ->flatMap(function ($location) {
                    return $location->associations->pluck('id');
                });
            $query->whereIn('id', $association_ids);
        }

        $query = $this->getAssociationList($query);
        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('#')->rowIndex()->size('sm'),
            TextColumn::make('name')->wrap(),
            TextColumn::make('benef')->label('No of Benef')
                ->formatStateUsing(fn ($record) => $record ? Beneficiary::where('association_id', $record->id)->count() : 0),
            TextColumn::make('total_deposit')
                ->formatStateUsing(fn ($record) => $record ? Deposit::where('association_id', $record->id)->sum('amount') : 0),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5];
    }

    protected function getTableFilters(): array
    {
        $locations = collect($this->getLocationIds());
        $query = Association::query();

        $super_admin = auth()->user()->roles->contains('id', 1);
        if (!$super_admin) {
            $association_ids = Location::with('associations')->where('parent_id', auth()->user()->location_id)->type('Ward')->get()
                ->flatMap(function ($location) {
                    return $location->associations->pluck('id');
                });
            $query->whereIn('id', $association_ids);
        }

        $query = $this->getAssociationList($query);
        return [
            Filter::make('filters')
                ->form([
                    Forms\Components\Select::make('association')
                        ->options($query->pluck('name', 'id'))
                        ->searchable()
                ])
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('depositWidget')->label('Details')
                ->action(function ($record) {
                    $this->emit('depositWidget', ['id' => $record->id]);
                })
        ];
    }
}
