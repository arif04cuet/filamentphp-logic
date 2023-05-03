<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Location;
use App\Models\User;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use XliteDev\FilamentImpersonate\Tables\Actions\ImpersonateAction;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                Card::make()
                    ->schema(
                        [
                            Grid::make(4)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->disableAutocomplete()
                                        ->maxLength(255),

                                    Forms\Components\TextInput::make('password')
                                        ->password()
                                        ->required()
                                        ->maxLength(255)
                                        ->disableAutocomplete()
                                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                        ->dehydrated(fn ($state) => filled($state))
                                        ->required(fn (string $context): bool => $context === 'create')
                                        ->disabled(fn ($record) => $record ? $record->id == auth()->user()->id : false),

                                    Forms\Components\Select::make('roles')
                                        ->multiple()
                                        ->relationship('roles', 'name')->preload()
                                        ->label('Roles'),
                                ]),

                            Grid::make(4)
                                ->schema([
                                    Forms\Components\Select::make('division')
                                        ->searchable()
                                        ->reactive()
                                        ->dehydrated(false)
                                        ->options(Location::query()->type('Division')->pluck('name', 'id'))
                                        ->afterStateUpdated(function (Closure $set, $state) {
                                            $set('district', null);
                                            $set('upazila', null);
                                            $set('location_id', null);
                                        })
                                        ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                            if ($record && $record->location && $record->location->location_type_id > 2)
                                                $record && $component->state($record->location->division->id);
                                        })
                                        ->hidden(fn ($record) => $record && $record->location ? $record->location->location_type_id < 3 : false),

                                    Forms\Components\Select::make('district')
                                        ->searchable()
                                        ->reactive()
                                        ->afterStateUpdated(function (Closure $set, $state) {
                                            $set('upazila', null);
                                            $set('location_id', null);
                                        })
                                        ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                            if ($record && $record->location && $record->location->location_type_id > 3)
                                                $record && $component->state($record->location->district->id);
                                        })
                                        ->options(function (callable $get) {
                                            return Location::query()
                                                ->where('parent_id', $get('division'))
                                                ->type('District')
                                                ->pluck('name', 'id');
                                        })
                                        ->hidden(fn ($record) => $record && $record->location ? $record->location->location_type_id < 4 : false),

                                    Forms\Components\Select::make('upazila')
                                        ->searchable()
                                        ->reactive()
                                        ->afterStateUpdated(function (Closure $set, $state) {
                                            $set('location_id', null);
                                        })
                                        ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                            if ($record && $record->location && $record->location->location_type_id > 4)
                                                $record && $component->state($record->location->upazila->id);
                                        })
                                        ->options(function (callable $get) {
                                            return Location::query()
                                                ->where('parent_id', $get('district'))
                                                ->type('Upazila')
                                                ->pluck('name', 'id');
                                        })
                                        ->hidden(fn ($record) => $record && $record->location ? $record->location->location_type_id < 5 : false),

                                    Forms\Components\Select::make('location_id')
                                        ->reactive()
                                        ->searchable()
                                        ->label(fn ($record) => $record && $record->location ? $record->location->locationType->name : 'Union')
                                        ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                            if ($record && $record->location && $record->location->location_type_id > 4)
                                                $record && $component->state($record->location->id);
                                        })
                                        ->options(function (callable $get, ?Model $record) {
                                            if ($record && $record->location && $record->location->location_type_id > 0 && $record->location->location_type_id < 5)
                                                return Location::query()->where('parent_id', $record->location->parent_id)->pluck('name', 'id');
                                            return Location::query()
                                                ->where('parent_id', $get('upazila'))
                                                ->type('Union')
                                                ->pluck('name', 'id');
                                        })
                                        ->required(),
                                ]),
                        ]
                    )->columns(3)
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex()->size('sm'),
                ImageColumn::make('photo')->circular(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                // Tables\Columns\TextColumn::make('email_2'),
                TextColumn::make('location.name')->searchable(),
                TextColumn::make('location.locationType.name')->searchable(),
                // Tables\Columns\TextColumn::make('email_verified_at')
                //     ->dateTime(),
                TextColumn::make('roles.name')->searchable()->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()->formatStateUsing(fn ($record) => $record ? $record->updated_at->addHours(6) : ''),
                TextColumn::make('updated_at')
                    ->dateTime()->formatStateUsing(fn ($record) => $record ? $record->updated_at->addHours(6) : ''),
            ])
            ->filters([
                //
            ])
            ->actions([
                ImpersonateAction::make()->visible(fn (User $user): bool => auth()->user()->can('impersonate', $user)),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
