<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrfBeneficiaryResource\Pages;
use App\Filament\Resources\CrfBeneficiaryResource\RelationManagers;
use App\Models\CrfBeneficiary;
use App\Models\CrfGroup;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Card;

class CrfBeneficiaryResource extends Resource
{
    protected static ?string $model = CrfBeneficiary::class;

    protected static ?string $navigationGroup = 'Old Data';
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('beneficiary_account_no')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('beneficiary_mobile')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('beneficiary_nid_br')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('beneficiary_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('district')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('division')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('father_name_spouse')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hh')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hh_head_mobile_no')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hh_head_nid_no_br')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hh_head_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mobile_number_type')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('new_hh_id')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('round'),
                        Forms\Components\TextInput::make('union')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('upazila')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('village_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ward')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('_id')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('crf_group_id')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location_id'),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('beneficiary_name')->searchable(),
                Tables\Columns\TextColumn::make('beneficiary_mobile')->searchable(),
                Tables\Columns\TextColumn::make('division')->searchable(),
                Tables\Columns\TextColumn::make('district')->searchable(),
                Tables\Columns\TextColumn::make('upazila')->searchable(),
                Tables\Columns\TextColumn::make('union')->searchable(),
                Tables\Columns\TextColumn::make('ward')->searchable(),
                Tables\Columns\TextColumn::make('village_name')->searchable(),
                Tables\Columns\TextColumn::make('group.group_name')->searchable(),
                Tables\Columns\TextColumn::make('new_hh_id')->searchable(),

                //Tables\Columns\TextColumn::make('location.name'),

                // Tables\Columns\TextColumn::make('father_name_spouse'),
                // Tables\Columns\TextColumn::make('beneficiary_nid_br'),
                // Tables\Columns\TextColumn::make('beneficiary_account_no'),
                // Tables\Columns\TextColumn::make('hh'),
                // Tables\Columns\TextColumn::make('hh_head_mobile_no'),
                // Tables\Columns\TextColumn::make('hh_head_nid_no_br'),
                // Tables\Columns\TextColumn::make('hh_head_name'),
                // Tables\Columns\TextColumn::make('mobile_number_type'),
                // Tables\Columns\TextColumn::make('round'),

                // Tables\Columns\TextColumn::make('_id'),
                // Tables\Columns\TextColumn::make('location_id'),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime(),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('crf_group_id')->label('Group')->options(CrfGroup::all()->pluck('group_name', 'group_id'))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCrfBeneficiaries::route('/'),
            'create' => Pages\CreateCrfBeneficiary::route('/create'),
            //'edit' => Pages\EditCrfBeneficiary::route('/{record}/edit'),
            'view' => Pages\ViewBeneficiary::route('/{record}'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
