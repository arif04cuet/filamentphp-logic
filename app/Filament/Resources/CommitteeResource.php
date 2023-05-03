<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommitteeResource\Pages;
use App\Filament\Resources\CommitteeResource\RelationManagers;
use App\Models\Association;
use App\Models\BeneficiaryCommittee;
use App\Models\Committee;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use App\Models\Location;

class CommitteeResource extends Resource
{
    protected static ?string $model = Committee::class;
    protected static ?string $navigationGroup = 'Association Management';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 6;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('title')->required(),
                                Select::make('association_id')->label('Association')
                                    ->options(function () {
                                        $locations = Location::with('associations')->where('parent_id', auth()->user()->location_id)->type('Ward')->get();
                                        $associations = collect();
                                        foreach ($locations as $location)
                                            $associations = $associations->merge($location->associations);
                                        $options = $associations->pluck('name', 'id');
                                        return $options;
                                    })->reactive()->searchable()->required()->disabledOn('edit'),
                                DatePicker::make('validity_from'),
                                DatePicker::make('validity_to')->after('validity_from'),
                            ]),
                        Textarea::make('objective'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex()->size('sm'),
                TextColumn::make('title'),
                TextColumn::make('objective')->wrap(),
                TextColumn::make('association.name'),
                TextColumn::make('validity_from')->date('M d, Y'),
                TextColumn::make('validity_to')->date('M d, Y'),
                TextColumn::make('no_of_beneficiaries')->formatStateUsing(fn ($record) => count($record->association->beneficiaries)),
                TextColumn::make('role_assigned_to')->formatStateUsing(fn ($record) => BeneficiaryCommittee::where('committee_id', $record->id)->count()),
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
            RelationManagers\BeneficiariesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCommittees::route('/'),
            'create' => Pages\CreateCommittee::route('/create'),
            'edit' => Pages\EditCommittee::route('/{record}/edit'),
        ];
    }
}
