<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResolutionResource\Pages;
use App\Filament\Resources\ResolutionResource\RelationManagers;
use App\Models\Association;
use App\Models\Resolution;
use App\Models\Location;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DateTimepicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResolutionResource extends Resource
{
    protected static ?string $model = Resolution::class;
    protected static ?string $navigationGroup = 'Association Management';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 4;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Grid::make()
                            ->schema([
                                Select::make('association_id')->label('Association')
                                    ->options(function () {
                                        $locations = Location::with('associations')->where('parent_id', auth()->user()->location_id)->type('Ward')->get();
                                        $associations = collect();
                                        foreach ($locations as $location)
                                            $associations = $associations->merge($location->associations);
                                        $options = $associations->pluck('name', 'id');
                                        return $options;
                                    })
                                    ->searchable(),
                                TextInput::make('title')->required(),
                            ]),
                        RichEditor::make('description'),
                        Grid::make()
                            ->schema([
                                Forms\Components\DateTimePicker::make('date_time')->label('Date & Time')->weekStartsOnSunday()->withoutSeconds()->required(),
                                FileUpload::make('file')->multiple()->enableDownload()->enableOpen()->directory('resolution-files'),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex()->size('sm'),
                TextColumn::make('title')->wrap()->searchable(),
                TextColumn::make('association.name')->wrap()->searchable(),
                TextColumn::make('date_time')->searchable(),
                TextColumn::make('file')->label('Total Added File')->formatStateUsing(fn ($record) => $record->file ? count($record->file) . ' File(s)' : 0)->wrap(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResolutions::route('/'),
            'create' => Pages\CreateResolution::route('/create'),
            'edit' => Pages\EditResolution::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     $locations = Location::with('associations')->where('parent_id', auth()->user()->location_id)->type('Ward')->get();
    //     $associations = collect();
    //     foreach ($locations as $location)
    //         $associations = $associations->merge($location->associations);
    //     $association_ids = $associations->pluck('id');
    //     return parent::getEloquentQuery()->whereIn('association_id', $association_ids);
    // }
}
