<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvestmentResource\Pages;
use App\Filament\Resources\InvestmentResource\RelationManagers;
use App\Models\Association;
use App\Models\Beneficiary;
use App\Models\Investment;
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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\MorphToSelect;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\Hidden;
use App\Models\BusinessTransaction;
use Filament\Notifications\Notification;
use App\Models\BusinessType;
use App\Models\BusinessSector;

class InvestmentResource extends Resource
{
    protected static ?string $model = Investment::class;
    protected static ?string $label = 'Businesses';
    protected static ?string $navigationGroup = 'Business Management';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        MorphToSelect::make('entity')->label('Business Owner')->reactive()
                            ->types([
                                MorphToSelect\Type::make(Association::class)->titleColumnName('name'),
                                MorphToSelect\Type::make(Beneficiary::class)->titleColumnName('beneficiary_name'),
                            ])->required(),
                        Hidden::make('association_id')->default(fn (Closure $get) => $get('entity_id')),
                        Fieldset::make('Business')
                            ->schema([
                                TextInput::make('title')->required(),
                                Select::make('business_type_id')->label('Business Type')->options(BusinessType::all()->pluck('title', 'id'))->searchable()->required(),
                                Select::make('income_sector_ids')->label('Income Sectors')->options(BusinessSector::where('type', 'income')->whereNot('parent_id', 0)->pluck('title', 'id'))->multiple()->searchable(),
                                Select::make('expense_sector_ids')->label('Expense Sectors')->options(BusinessSector::where('type', 'expense')->whereNot('parent_id', 0)->pluck('title', 'id'))->multiple()->searchable(),
                                TextInput::make('amount'),
                                DatePicker::make('from_date')->label('Business Start Date')->weekStartsOnSunday()->required(),
                                DatePicker::make('to_date')->label('Tentative Return Date')->weekStartsOnSunday(),
                                Toggle::make('status')->inline(false)->default('on'),
                                Fieldset::make('Location')
                                    ->schema([
                                        Select::make('division')->searchable()->reactive()
                                            ->afterStateUpdated(function (Closure $set, $state) {
                                                $set('district', null);
                                                $set('upazila', null);
                                                $set('union', null);
                                            })
                                            ->afterStateHydrated(function (Closure $get, $component, $state, $record) {
                                                if ($record && $record->location) {
                                                    $location = $record->location;
                                                    $record && $component->state($location->division->id);
                                                }
                                            })
                                            ->options(Location::query()->type('Division')->pluck('name', 'id'))->searchable(),
                                        Select::make('district')->searchable()->reactive()
                                            ->afterStateUpdated(function (Closure $set, $state) {
                                                $set('upazila', null);
                                                $set('union', null);
                                            })
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->location) {
                                                    $location = $record->location;
                                                    $record && $component->state($location->district->id);
                                                }
                                            })
                                            ->options(function (callable $get) {
                                                return Location::query()
                                                    ->where('parent_id', $get('division'))
                                                    ->type('district')
                                                    ->pluck('name', 'id');
                                            }),
                                        Select::make('upazila')->searchable()->reactive()
                                            ->afterStateUpdated(function (Closure $set, $state) {
                                                $set('union', null);
                                            })
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->location) {
                                                    $location = $record->location;
                                                    $record && $component->state($location->upazila->id);
                                                }
                                            })
                                            ->options(function (callable $get) {
                                                return Location::query()
                                                    ->where('parent_id', $get('district'))
                                                    ->type('Upazila')
                                                    ->pluck('name', 'id');
                                            }),
                                        Select::make('location_id')->label('Union')->searchable()->reactive()
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->location) {
                                                    $location = $record->location;
                                                    $record && $component->state($location->id);
                                                }
                                            })
                                            ->options(function (callable $get) {
                                                return Location::query()
                                                    ->where('parent_id', $get('upazila'))
                                                    ->type('Union')
                                                    ->pluck('name', 'id');
                                            })->requiredWith('division'),
                                    ])->hidden(fn (Closure $get) => $get('entity_type') == null)
                                    ->columns(4),
                            ])->columns(4),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('#')->rowIndex()->size('sm'),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('investment_type')->label('Business Owner')->formatStateUsing(fn ($record) => $record->investment_type ? Investment::investmentType($record->investment_type) : '')
                    ->description(function ($record) {
                        if ($record->investment_type == 1)
                            return "Association Title: " . $record->entity->name;
                        else
                            return "";
                    })->wrap(),
                TextColumn::make('businessType.title')->searchable()->wrap(),
                TextColumn::make('location.name')->label('Location-Union')->searchable(),
                TextColumn::make('amount')->sortable()->searchable(),
                TextColumn::make('from_date')->label('Start Date')->date('M d, Y'),
                TextColumn::make('to_date')->label('Tentative Return Date')->date('M d, Y'),
                TextColumn::make('income_sector_ids')->label('Income Sectors')->formatStateUsing(fn ($record) => $record->income_sector_ids ? count($record->income_sector_ids) : 0),
                TextColumn::make('expense_sector_ids')->label('Expense Sectors')->formatStateUsing(fn ($record) => $record->expense_sector_ids ? count($record->expense_sector_ids) : 0),
                TextColumn::make('no_of_transc')->label('Transactions')->formatStateUsing(fn ($record) => $record->businessTransactions->count()),
                ToggleColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('income')
                        ->form([
                            Select::make('code_id')->label('Income Sector')->options(function ($record) {
                                if ($record->income_sector_ids)
                                    return BusinessSector::whereIn('id', $record->income_sector_ids)->pluck('title', 'id')->toArray();
                            })->searchable()->required(),
                            TextInput::make('amount')->numeric()->required(),
                            DatePicker::make('transaction_date')->label('Transaction Date')->required(),
                        ])
                        ->action(function ($record, array $data): void {
                            $transc = BusinessTransaction::insert([
                                'business_id' => $record->id,
                                'type' => BusinessTransaction::TYPE_INCOME,
                                'code_id' => $data['code_id'],
                                'amount' => $data['amount'],
                                'transaction_date' => $data['transaction_date'],
                            ]);
                            if ($transc)
                                Notification::make()->success()->title('Income Transaction Added');
                        })
                        ->modalFooter(function ($record) {
                            return view('filament.pages.business-profit', ['type' => 'income', 'id' => $record->id]);
                        })
                        ->icon('heroicon-o-chevron-double-up'),
                    Tables\Actions\Action::make('expense')
                        ->form([
                            Select::make('code_id')->label('Expense Sector')->options(function ($record) {
                                if ($record->expense_sector_ids)
                                    return BusinessSector::whereIn('id', $record->expense_sector_ids)->pluck('title', 'id')->toArray();
                            })->searchable()->required(),
                            TextInput::make('amount')->numeric()->required(),
                            DatePicker::make('transaction_date')->label('Transaction Date')->required(),
                        ])
                        ->action(function ($record, array $data): void {
                            $transc = BusinessTransaction::insert([
                                'business_id' => $record->id,
                                'type' => BusinessTransaction::TYPE_EXPENSE,
                                'code_id' => $data['code_id'],
                                'amount' => $data['amount'],
                                'transaction_date' => $data['transaction_date'],
                            ]);
                            if ($transc)
                                Notification::make()->success()->title('Expense Transaction Added');
                        })
                        ->modalFooter(function ($record) {
                            return view('filament.pages.business-profit', ['type' => 'expense', 'id' => $record->id]);
                        })
                        ->icon('heroicon-o-chevron-double-down')->color('danger'),
                    Tables\Actions\Action::make('profit')
                        ->action(function () {
                        })
                        ->modalContent(function ($record) {
                            return view('filament.pages.business-profit', ['type' => null, 'id' => $record->id]);
                        })
                        ->icon('heroicon-o-document-text')->color('warning'),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListInvestments::route('/'),
            'create' => Pages\CreateInvestment::route('/create'),
            'edit' => Pages\EditInvestment::route('/{record}/edit'),
        ];
    }

    public static function getSlug(): string
    {
        return 'businesses';
    }
}
