<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommitteeRoleResource\Pages;
use App\Filament\Resources\CommitteeRoleResource\RelationManagers;
use App\Models\CommitteeRole;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommitteeRoleResource extends Resource
{
    protected static ?string $model = CommitteeRole::class;
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex()->size('sm'),
                TextColumn::make('name'),
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
            'index' => Pages\ListCommitteeRoles::route('/'),
            'create' => Pages\CreateCommitteeRole::route('/create'),
            'edit' => Pages\EditCommitteeRole::route('/{record}/edit'),
        ];
    }
    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
