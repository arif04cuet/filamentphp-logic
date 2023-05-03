<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssociationResource\Pages;
use App\Models\Association;
use App\Models\CrfBeneficiary;
use App\Models\Beneficiary;
use App\Models\CrfGroup;
use App\Models\Location;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Illuminate\Support\HtmlString;
use Closure;
use App\Models\CommitteeRole;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\AssociationResource\RelationManagers;

class AssociationResource extends Resource
{
    protected static ?string $model = Association::class;
    protected static ?string $navigationGroup = 'Association Management';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Fieldset::make('Association Information')
                            ->schema([
                                TextInput::make('name'),
                                Select::make('deposit_type')->options(Association::depositType())->searchable(),
                                Select::make('share_type')->options(Association::shareType())->searchable(),
                                TextInput::make('beneficiary_no'),
                            ])
                            ->columns(4),
                        Fieldset::make('Association Location')
                            ->schema([
                                Select::make('division')->searchable()->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('district', null);
                                        $set('upazila', null);
                                        $set('union', null);
                                        $set('location_id', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $location = $record->locations->first();
                                            $record && $component->state($location->division->id);
                                        }
                                    })
                                    ->options(Location::query()->type('Division')->pluck('name', 'id'))->required(),
                                Select::make('district')->searchable()->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('upazila', null);
                                        $set('union', null);
                                        $set('location_id', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $location = $record->locations->first();
                                            $record && $component->state($location->district->id);
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('division'))
                                            ->type('district')
                                            ->pluck('name', 'id');
                                    })->required(),
                                Select::make('upazila')->searchable()->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('union', null);
                                        $set('location_id', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                        if ($record) {
                                            $location = $record->locations->first();
                                            $record && $component->state($location->upazila->id);
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('district'))
                                            ->type('Upazila')
                                            ->pluck('name', 'id');
                                    })->required(),
                                Select::make('union')->searchable()->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('location_id', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                        if ($record) {
                                            $location = $record->locations->first();
                                            $record && $component->state($location->union->id);
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('upazila'))
                                            ->type('Union')
                                            ->pluck('name', 'id');
                                    })->required(),
                                Select::make('location_id')->reactive()->searchable()->label('Ward')->multiple()
                                    ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                        if ($record) {
                                            $locations = $record->locations;
                                            foreach ($locations as $loc)
                                                $id[] = $loc->id;
                                            $record && $component->state($id);
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('union'))
                                            ->type('ward')
                                            ->pluck('name', 'id');
                                    })->required(),
                            ])
                            ->columns(5),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex()->size('sm'),
                TextColumn::make('name')->wrap()->searchable(),
                TextColumn::make('locations.name')->label('Wards')->searchable(),
                TextColumn::make('deposit_type')->formatStateUsing(fn ($record) => $record->deposit_type ? Association::depositType($record->deposit_type) : ''),
                TextColumn::make('share_type')->formatStateUsing(fn ($record) => $record->share_type ? Association::shareType($record->share_type) : ''),
                // TextColumn::make('beneficiry_no')->label('Max Beneficiary')->searchable(),
                TextColumn::make('beneficiary_count')->formatStateUsing(fn ($record) => Beneficiary::where('association_id', $record->id)->count())->wrap(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('beneficiaries')->icon('heroicon-o-user')
                    ->url(
                        fn ($record): string => url('/admin/beneficiaries') . '?tableFilters[association_id][value]=' . $record->id
                    ),
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BeneficiariesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssociations::route('/'),
            'create' => Pages\CreateAssociation::route('/create'),
            'edit' => Pages\EditAssociation::route('/{record}/edit'),
            'view' => Pages\ViewAssociation::route('/view/{record}'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     $locations = Location::with('associations')->where('parent_id', auth()->user()->location_id)->type('Ward')->get();
    //     $associations = collect();
    //     foreach ($locations as $location)
    //         $associations = $associations->merge($location->associations);
    //     $association_ids = $associations->pluck('id');
    //     return parent::getEloquentQuery()->whereIn('id', $association_ids);
    // }
    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
