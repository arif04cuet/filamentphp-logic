<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationTypeResource\Pages;
use App\Filament\Resources\LocationTypeResource\RelationManagers;
use App\Models\LocationType;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;

class LocationTypeResource extends Resource
{
    protected static ?string $model = LocationType::class;

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 4;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('#')->rowIndex()->size('sm'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->formatStateUsing(fn ($record) => $record ? $record->updated_at->addHours(6) : ''),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->formatStateUsing(fn ($record) => $record ? $record->updated_at->addHours(6) : ''),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocationTypes::route('/'),
            'create' => Pages\CreateLocationType::route('/create'),
            'edit' => Pages\EditLocationType::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
