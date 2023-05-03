<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CrfGroupResource\Pages;
use App\Filament\Resources\CrfGroupResource\RelationManagers;
use App\Models\CrfGroup;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;

class CrfGroupResource extends Resource
{
    protected static ?string $model = CrfGroup::class;

    protected static ?string $navigationGroup = 'Old Data';
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('group_id')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location_id'),
                        Forms\Components\TextInput::make('ward_0_value')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ward_0_label')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ward_1_value')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ward_1_label')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('district')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('upazila')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('union')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('crf_round'),
                        Forms\Components\TextInput::make('group_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('group_address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('male_beneficiaries'),
                        Forms\Components\TextInput::make('female_beneficiaries'),
                        Forms\Components\TextInput::make('crf_beneficiaries'),
                        Forms\Components\TextInput::make('livelihood_started')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('group_account_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('group_account_number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('routing_number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_branch_address')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('money_received'),
                        Forms\Components\TextInput::make('money_invested'),
                        Forms\Components\TextInput::make('remarks')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bank_branch_name')
                            ->maxLength(255),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_name')->searchable(),
                //Tables\Columns\TextColumn::make('group_id'),
                Tables\Columns\TextColumn::make('location.name')->searchable(),
                //Tables\Columns\TextColumn::make('group_address')->label('address'),
                Tables\Columns\TextColumn::make('male_beneficiaries')->label('Male'),
                Tables\Columns\TextColumn::make('female_beneficiaries')->label('Female'),
                Tables\Columns\TextColumn::make('crf_beneficiaries')->label('Total'),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime(),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('members')->icon('heroicon-o-user')
                    ->url(
                        fn ($record): string => url('/admin/crf-beneficiaries') . '?tableFilters[crf_group_id][value]=' . $record->group_id
                    ),
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
            'index' => Pages\ListCrfGroups::route('/'),
            'create' => Pages\CreateCrfGroup::route('/create'),
            //'edit' => Pages\EditCrfGroup::route('/{record}/edit'),
            'view' => Pages\ViewGroup::route('/{record}'),
        ];
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
