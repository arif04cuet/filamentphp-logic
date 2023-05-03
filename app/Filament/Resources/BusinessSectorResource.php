<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessSectorResource\Pages;
use App\Filament\Resources\BusinessSectorResource\RelationManagers;
use App\Models\BusinessSector;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class BusinessSectorResource extends Resource
{
    protected static ?string $model = BusinessSector::class;
    protected static ?string $navigationGroup = 'Business Management';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        Select::make('type')->options(BusinessSector::businessSectorType())->searchable()
                            ->disabled(fn ($record) => $record ? $record->parent_id == 0 : false)->required(),
                        TextInput::make('title')->required(),
                        TextInput::make('code_id')->label('Code')->unique(ignoreRecord: true)->required(),
                        Select::make('parent_id')->label('Parent')->options(BusinessSector::all()->pluck('title', 'id'))->searchable()
                            ->disabled(fn ($record) => $record ? $record->parent_id == 0 : false)->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex()->size('sm'),
                TextColumn::make('title')->searchable(),
                TextColumn::make('type')->searchable()->sortable(),
                TextColumn::make('code_id')->searchable(),
                TextColumn::make('parent.title')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBusinessSectors::route('/'),
        ];
    }
}
