<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositResource\Pages;
use App\Filament\Resources\DepositResource\RelationManagers;
use App\Models\Beneficiary;
use App\Models\Deposit;
use App\Models\Location;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;

class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;
    protected static ?string $navigationGroup = 'Association Management';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('association_id')->label('Association')
                                    ->options(function (Closure $get, $set) {
                                        $set('beneficiary_count', Beneficiary::where('association_id', $get('association_id'))->count());
                                        $locations = Location::with('associations')->where('parent_id', auth()->user()->location_id)->type('Ward')->get();
                                        $associations = collect();
                                        foreach ($locations as $location)
                                            $associations = $associations->merge($location->associations);
                                        $options = $associations->pluck('name', 'id');
                                        return $options;
                                    })->searchable()->reactive()->required()->disabledOn('edit'),
                                Select::make('beneficiary_option')->label('Beneficiary Option')
                                    ->options([
                                        'all' => 'All Beneficiary',
                                        'selected' => 'Selected Beneficiary',
                                    ])->hidden(fn (Closure $get) => $get('association_id') == null)
                                    ->searchable()->reactive()->required()->visibleOn('create'),
                                Select::make('beneficiary_id')->label('Select One Beneficiary')
                                    ->options(function (Closure $get) {
                                        return Beneficiary::where('association_id', $get('association_id'))->pluck('beneficiary_name', 'id');
                                    })->hidden(fn (Closure $get) => $get('beneficiary_option') !== 'selected')
                                    ->searchable()->reactive()->required()->visibleOn('create'),
                                TextInput::make('beneficiary_count')->hidden(fn (Closure $get) => $get('beneficiary_option') !== 'all')->disabled()->visibleOn('create'),
                                Select::make('beneficiary_id')->label('Select One Beneficiary')
                                    ->options(function (Closure $get) {
                                        $association_id = $get('association_id');
                                        return Beneficiary::where('association_id', $association_id)->pluck('beneficiary_name', 'id');
                                    })->searchable()->required()->visibleOn('edit'),
                            ]),
                        Grid::make(5)
                            ->schema([
                                TextInput::make('name')->required(),
                                TextInput::make('reference_id'),
                                DatePicker::make('deposit_date')->required(),
                                TextInput::make('amount')->numeric()->required(),
                                FileUpload::make('file')->label('Attachment')->multiple()->enableDownload()->enableOpen()->directory('deposit-files'),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('reference_id'),
                TextColumn::make('deposit_date')->date('M d, Y'),
                TextColumn::make('association.name')->wrap(),
                TextColumn::make('beneficiary.beneficiary_name')->wrap(),
                TextColumn::make('amount'),
                TextColumn::make('created_at')->formatStateUsing(fn ($record) => $record ? $record->updated_at->addHours(6) : ''),
                TextColumn::make('updated_at')->formatStateUsing(fn ($record) => $record ? $record->updated_at->addHours(6) : ''),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDeposits::route('/'),
            'create' => Pages\CreateDeposit::route('/create'),
            'edit' => Pages\EditDeposit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('id', 'DESC');
    }
}
