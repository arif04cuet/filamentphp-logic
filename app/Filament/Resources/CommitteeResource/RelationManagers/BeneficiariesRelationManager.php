<?php

namespace App\Filament\Resources\CommitteeResource\RelationManagers;

use App\Filament\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use App\Models\BeneficiaryCommittee;
use App\Models\CommitteeRole;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;

class BeneficiariesRelationManager extends RelationManager
{
    protected static string $relationship = 'beneficiaries';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('beneficiary_id')->label('Beneficiary')->searchable()->required()
                    ->options(function (RelationManager $livewire, $record) {
                        if ($record)
                            return Beneficiary::where('id', $record->beneficiary_id)->pluck('beneficiary_name', 'id');
                        else {
                            $committee_id = $livewire->ownerRecord->id;
                            $beneficiaries = Beneficiary::where('association_id', $livewire->ownerRecord->association_id)
                                ->whereNotIn('id', function ($query) use ($committee_id) {
                                    $query->select('beneficiary_id')
                                        ->from('beneficiary_committee')
                                        ->where('committee_id', $committee_id);
                                })
                                ->pluck('beneficiary_name', 'id');
                            return $beneficiaries;
                        }
                    }),
                Select::make('committee_role_id')->label('Committee Role')->searchable()->required()
                    ->options(CommitteeRole::all()->pluck('name', 'id'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex()->size('sm'),
                TextColumn::make('beneficiary.beneficiary_name'),
                TextColumn::make('committeeRole.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Add Beneficiary & Role'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
