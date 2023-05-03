<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeneficiaryResource\Pages;
use App\Filament\Resources\BeneficiaryResource\RelationManagers;
use App\Models\Association;
use App\Models\Beneficiary;
use App\Models\CrfGroup;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BeneficiaryResource extends Resource
{
    protected static ?string $model = Beneficiary::class;
    protected static ?string $navigationGroup = 'Association Management';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('#')->rowIndex()->size('sm'),
                TextColumn::make('association.name')->searchable()->wrap()->size('sm'),
                TextColumn::make('beneficiary_name')->searchable()->wrap()->size('sm'),
                TextColumn::make('beneficiary_mobile')->searchable()->size('sm'),
                TextColumn::make('location.division.name')->searchable()->size('sm'),
                TextColumn::make('location.district.name')->searchable()->size('sm'),
                TextColumn::make('location.upazila.name')->searchable()->size('sm'),
                TextColumn::make('location.union.name')->searchable()->wrap()->size('sm'),
                TextColumn::make('location.ward.name')->searchable()->wrap()->size('sm'),
                TextColumn::make('village_name')->formatStateUsing(function ($record) {
                    $type = $record->location->locationType->id;
                    if ($type === 7)
                        return $record->location->name;
                    else
                        return '';
                })->wrap()->size('sm'),
                TextColumn::make('crfBeneficiary.new_hh_id')->label('New hh id')->searchable()->size('sm'),
                TextColumn::make('crfBeneficiary.group.group_name')->searchable()->wrap()->size('sm'),
                TextColumn::make('nominee.nominee_name')->searchable()->wrap()->size('sm'),
                TextColumn::make('nominee.location.name')->label('Nominee Location')->searchable()->wrap()->size('sm'),
            ])
            ->filters([
                SelectFilter::make('association_id')->label('Association Title')->options(Association::all()->pluck('name', 'id'))
            ])
            ->actions([])
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
            'index' => Pages\ListBeneficiaries::route('/'),
            // 'create' => Pages\CreateBeneficiary::route('/create'),
            // 'edit' => Pages\EditBeneficiary::route('/{record}/edit'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
