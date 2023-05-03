<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssociationMigratorResource\Pages;
use App\Filament\Resources\AssociationResource\Pages\ListAssociations;
use App\Models\AssociationMigrator;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use App\Models\CrfBeneficiary;
use App\Models\Beneficiary;
use App\Models\CrfGroup;
use App\Models\Location;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Illuminate\Support\HtmlString;
use Closure;

class AssociationMigratorResource extends Resource
{
    protected static ?string $model = AssociationMigrator::class;
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Create Association')
                                ->schema([
                                    TextInput::make('name')->label('Enter Association Title')->validationAttribute('title')->reactive()
                                        ->afterStateUpdated(function (Closure $set, $state) {
                                            $set('association_title', $state);
                                        })->required(),
                                    Select::make('ward_name')->label(auth()->user()->location ? 'Wards Under ' . auth()->user()->location->name : 'Wards')->validationAttribute('ward')
                                        ->options(Location::query()->groupBy('name')->where('parent_id', auth()->user()->location_id)->type('Ward')->pluck('name', 'name'))
                                        ->searchable()->multiple()->reactive()
                                        ->afterStateUpdated(function (Closure $set, $state) {
                                            $set('selected_ward(s)', $state);
                                        })
                                        ->required()->disablePlaceholderSelection()
                                ]),
                            Wizard\Step::make('Add Beneficiaries')
                                ->schema([
                                    Select::make('group')->label('Select Group')->validationAttribute('group')->reactive()
                                        ->options(function (Closure $get) {
                                            $ward_name = $get('ward_name');
                                            $groups = CrfGroup::where('location_id', auth()->user()->location_id)->where(
                                                function ($query) use ($ward_name) {
                                                    $query->whereIn('ward_0_label', $ward_name)->orWhereIn('ward_1_label', $ward_name);
                                                }
                                            )->get();
                                            foreach ($groups as $group) {
                                                $group->new_name = $group->ward_1_label ? $group->group_name . ' (Ward ' . $group->ward_0_label . ')' . ' (Ward ' . $group->ward_1_label . ')' : $group->group_name . ' (Ward ' . $group->ward_0_label . ')';
                                            }
                                            $options = $groups->pluck('new_name', 'group_id');
                                            return $options;
                                        })->searchable()->required(),
                                    Grid::make(['sm' => 2])
                                        ->schema([
                                            CheckboxList::make('beneficiaries')->label(fn (Closure $get) => 'Available Beneficiaries (' . CrfBeneficiary::whereNotNull('crf_group_id')->whereNull('is_used')->where('crf_group_id', $get('group'))->count() . ')')->validationAttribute('beneficiaries')
                                                ->reactive()->options(function (Closure $get) {
                                                    $beneficiaries = CrfBeneficiary::whereNotNull('crf_group_id')->whereNull('is_used')->where('crf_group_id', $get('group'))->get();
                                                    foreach ($beneficiaries as $beneficiary) {
                                                        $beneficiary->beneficiary_new_name = $beneficiary->beneficiary_name . ' (' . $beneficiary->hh . ')';
                                                    }
                                                    $options = $beneficiaries->pluck('beneficiary_new_name', 'id');
                                                    return $options;
                                                })
                                                ->required(),
                                            CheckboxList::make('added_beneficiaries')->label(fn (Closure $get) => 'Selected Beneficiaries (' . count($get('beneficiaries')) . ')')
                                                ->options(function (Closure $get) {
                                                    $beneficiaries = $get('beneficiaries');
                                                    $new_list = [];
                                                    foreach ($beneficiaries as $beneficiary) {
                                                        $benef_info = CrfBeneficiary::find($beneficiary);
                                                        $new_list[] = $benef_info->beneficiary_name . ' (' . $benef_info->hh . ')' . ' (' . $benef_info->group->group_name . ')';
                                                    }
                                                    return $new_list;
                                                })->disabled(),
                                        ]),
                                ]),
                            Wizard\Step::make('Preview')
                                ->schema([
                                    TextInput::make('association_title')->disabled(),
                                    Select::make('selected_ward(s)')->label(auth()->user()->location ? 'Selected Wards Under ' . auth()->user()->location->name : 'Selected Wards')->multiple()->disablePlaceholderSelection()->disabled(),
                                    CheckboxList::make('final_beneficiaries_list')->label(fn (Closure $get) => 'Final Beneficiary List (' . count($get('beneficiaries')) . ')')
                                        ->options(function (Closure $get) {
                                            $beneficiaries = $get('beneficiaries');
                                            $new_list = [];
                                            foreach ($beneficiaries as $beneficiary) {
                                                $benef_info = CrfBeneficiary::find($beneficiary);
                                                $new_list[] = $benef_info->beneficiary_name . ' (' . $benef_info->hh . ')' . ' (' . $benef_info->group->group_name . ')';
                                            }
                                            return $new_list;
                                        })->disabled(),
                                ]),
                        ])->submitAction(new HtmlString('<button type="submit" style="background-color: #52b678; border-radius: 10px; color: white; padding: 5px 15px; cursor: pointer;">Save</button>'))
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssociations::route('/'),
            'create' => Pages\CreateAssociationMigrator::route('/create'),
            'edit' => Pages\EditAssociationMigrator::route('/{record}/edit'),
        ];
    }
}
