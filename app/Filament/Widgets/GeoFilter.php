<?php

namespace App\Filament\Widgets;

use App\Models\Location;
use Closure;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class GeoFilter extends Widget implements HasForms
{
    use InteractsWithForms, HasWidgetShield;


    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public $title;
    public $content;

    public function mount(): void
    {
        $this->form->fill([
            'title' => '1',
            'content' => '2',
        ]);
    }


    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->columns(4)
                ->schema([
                    Forms\Components\Select::make('division')
                        ->searchable()
                        ->reactive()
                        ->dehydrated(false)
                        ->options(Location::query()->type('Division')->pluck('name', 'id'))
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('district', null);
                            $set('upazila', null);
                            $set('union', null);
                            $this->emit('filteredGeo', [
                                'type' => 'division',
                                'id' => $state
                            ]);
                        }),

                    Forms\Components\Select::make('district')
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('upazila', null);
                            $set('union', null);
                            $this->emit('filteredGeo', [
                                'type' => 'district',
                                'id' => $state
                            ]);
                        })
                        ->options(function (callable $get) {
                            return Location::query()
                                ->where('parent_id', $get('division'))
                                ->type('District')
                                ->pluck('name', 'id');
                        }),

                    Forms\Components\Select::make('upazila')
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('union', null);
                            $this->emit('filteredGeo', [
                                'type' => 'upazila',
                                'id' => $state
                            ]);
                        })
                        ->options(function (callable $get) {
                            return Location::query()
                                ->where('parent_id', $get('district'))
                                ->type('Upazila')
                                ->pluck('name', 'id');
                        }),

                    Forms\Components\Select::make('union')
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $this->emit('filteredGeo', [
                                'type' => 'union',
                                'id' => $state
                            ]);
                        })
                        ->options(function (callable $get) {
                            return Location::query()
                                ->where('parent_id', $get('upazila'))
                                ->type('Union')
                                ->pluck('name', 'id');
                        }),

                ])
        ];
    }

    public function submit(): void
    {
        logger($this->form->getState());
    }


    protected static string $view = 'filament.widgets.geo-filter';
}
