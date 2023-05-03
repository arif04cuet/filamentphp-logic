<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Filament\Resources\LocationResource\RelationManagers;
use App\Filament\Resources\LocationResource\RelationManagers\LocationTypeRelationManager;
use App\Models\Location;
use App\Models\LocationType;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Fieldset;
use Closure;
use Illuminate\Database\Eloquent\Model;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Forms\Components\Select::make('location_type_id')->label('Location Type')
                                    ->options(fn () => LocationType::query()->where('id', '<>', 1)->orderBy('id')->pluck('name', 'id')->toArray())
                                    ->reactive()->required(),
                                Forms\Components\TextInput::make('name')->label(fn (Closure $get) => 'Name of ' . LocationType::where('id', $get('location_type_id'))->pluck('name')->first())
                                    ->maxLength(255)->required(),
                            ]),
                        Fieldset::make('Select Parent')
                            ->schema([
                                Forms\Components\Select::make('division')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->locationType->id > 2)
                                            $record && $component->state($record->division->id);
                                    })
                                    ->options(Location::query()->type('Division')->pluck('name', 'id'))
                                    ->hidden(fn (Closure $get) => $get('location_type_id') < 3)
                                    ->reactive()->required(),
                                Forms\Components\Select::make('district')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->locationType->id > 3)
                                            $record && $component->state($record->district->id);
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('division'))
                                            ->type('District')
                                            ->pluck('name', 'id');
                                    })
                                    ->hidden(fn (Closure $get) => $get('location_type_id') < 4)
                                    ->reactive()->required(),
                                Forms\Components\Select::make('upazila')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->locationType->id > 4)
                                            $record && $component->state($record->upazila->id);
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('district'))
                                            ->type('Upazila')
                                            ->pluck('name', 'id');
                                    })
                                    ->hidden(fn (Closure $get) => $get('location_type_id') < 5)
                                    ->reactive()->required(),
                                Forms\Components\Select::make('union')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->locationType->id > 5)
                                            $record && $component->state($record->union->id);
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('upazila'))
                                            ->type('Union')
                                            ->pluck('name', 'id');
                                    })
                                    ->hidden(fn (Closure $get) => $get('location_type_id') < 6)
                                    ->reactive()->required(),
                                Forms\Components\Select::make('ward')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->locationType->id > 6)
                                            $record && $component->state($record->ward->id);
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('union'))
                                            ->type('ward')
                                            ->pluck('name', 'id');
                                    })
                                    ->hidden(fn (Closure $get) => $get('location_type_id') < 7)
                                    ->required()
                            ])->hidden(fn (Closure $get) => $get('location_type_id') < 3)
                            ->columns(5),

                        // Forms\Components\TextInput::make('user_id'),
                        Forms\Components\Toggle::make('logic_location')->inline(false)->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('parent.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('locationType.name')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('user_id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('logic_location')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->formatStateUsing(fn ($record) => $record ? $record->updated_at->addHours(6) : ''),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->formatStateUsing(fn ($record) => $record ? $record->updated_at->addHours(6) : ''),
            ])
            ->filters([
                SelectFilter::make('location_type_id')->label('Location Type')->options(LocationType::all()->pluck('name', 'id'))->multiple()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
