<?php

namespace App\Filament\Resources\AssociationResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use App\Models\CrfBeneficiary;
use App\Models\CrfGroup;
use App\Models\Beneficiary;
use App\Models\Location;
use Closure;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Models\Nominee;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BeneficiariesRelationManager extends RelationManager
{
    protected static string $relationship = 'Beneficiaries';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Personal Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('beneficiary_name')->required(),
                                TextInput::make('age'),
                                TextInput::make('occupation'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('father_name_spouse')->label('Father Name'),
                                TextInput::make('mother_name'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('beneficiary_mobile')->required(),
                                TextInput::make('beneficiary_nid_br'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('applicant_photo')->label('Photo')->multiple()->enableDownload()->enableOpen()->directory('beneficiary-applicant-files'),
                                FileUpload::make('applicant_signature')->label('Signature')->multiple()->enableDownload()->enableOpen()->directory('beneficiary-applicant-files'),
                            ])
                    ]),
                Fieldset::make('Beneficiary Location Information')
                    ->schema([
                        Select::make('division')->searchable()->reactive()->required()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('district', null);
                                $set('upazila', null);
                                $set('union', null);
                                $set('ward', null);
                                $set('village', null);
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $location = $record->location;
                                    $record && $component->state($location->division->id);
                                }
                            })
                            ->options(Location::query()->type('Division')->pluck('name', 'id')),
                        Select::make('district')->searchable()->reactive()->required()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('upazila', null);
                                $set('union', null);
                                $set('ward', null);
                                $set('village', null);
                            })
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
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
                        Select::make('upazila')->searchable()->reactive()->required()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('union', null);
                                $set('ward', null);
                                $set('village', null);
                            })
                            ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                if ($record) {
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
                        Select::make('union')->searchable()->reactive()->required()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('ward', null);
                                $set('village', null);
                            })
                            ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                if ($record) {
                                    $location = $record->location;
                                    $record && $component->state($location->union->id);
                                }
                            })
                            ->options(function (callable $get) {
                                return Location::query()
                                    ->where('parent_id', $get('upazila'))
                                    ->type('Union')
                                    ->pluck('name', 'id');
                            }),
                        Select::make('ward')->reactive()->searchable()->required()
                            ->afterStateUpdated(function (Closure $set, $state) {
                                $set('village', null);
                            })
                            ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                if ($record) {
                                    $location = $record->location;
                                    $record && $component->state($location->ward->id);
                                }
                            })
                            ->options(function (callable $get) {
                                return Location::query()
                                    ->where('parent_id', $get('union'))
                                    ->type('ward')
                                    ->pluck('name', 'id');
                            }),
                        Select::make('village')->searchable()->required()
                            ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                if ($record) {
                                    $loc = $record->location;
                                    $record && $component->state($loc->id);
                                }
                            })
                            ->options(function (callable $get) {
                                return Location::query()
                                    ->where('parent_id', $get('ward'))
                                    ->type('village')
                                    ->pluck('name', 'id');
                            }),
                    ])
                    ->columns(3),
                Fieldset::make('Nominee Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nominee_name'),
                                TextInput::make('relation_with_beneficiary'),
                            ]),
                        Fieldset::make('Nominee Location Information')
                            ->schema([
                                Select::make('nominee_division')->searchable()->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('nominee_district', null);
                                        $set('nominee_upazila', null);
                                        $set('nominee_union', null);
                                        $set('nominee_ward', null);
                                        $set('nominee_village', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->nominee && $record->nominee->location) {
                                            $component->state($record->nominee->location->division->id);
                                        }
                                    })
                                    ->options(Location::query()->type('Division')->pluck('name', 'id')),
                                Select::make('nominee_district')->searchable()->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('nominee_upazila', null);
                                        $set('nominee_union', null);
                                        $set('nominee_ward', null);
                                        $set('nominee_village', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->nominee && $record->nominee->location) {
                                            $component->state($record->nominee->location->district->id);
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('nominee_division'))
                                            ->type('district')
                                            ->pluck('name', 'id');
                                    }),
                                Select::make('nominee_upazila')->searchable()->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('nominee_union', null);
                                        $set('nominee_ward', null);
                                        $set('nominee_village', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                        if ($record && $record->nominee && $record->nominee->location) {
                                            $component->state($record->nominee->location->upazila->id);
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('nominee_district'))
                                            ->type('Upazila')
                                            ->pluck('name', 'id');
                                    }),
                                Select::make('nominee_union')->searchable()->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('nominee_ward', null);
                                        $set('nominee_village', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                        if ($record && $record->nominee && $record->nominee->location) {
                                            $component->state($record->nominee->location->union->id);
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('nominee_upazila'))
                                            ->type('Union')
                                            ->pluck('name', 'id');
                                    }),
                                Select::make('nominee_ward')->reactive()->searchable()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        $set('nominee_village', null);
                                    })
                                    ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                        if ($record && $record->nominee && $record->nominee->location) {
                                            $component->state($record->nominee->location->ward->id);
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('nominee_union'))
                                            ->type('ward')
                                            ->pluck('name', 'id');
                                    }),
                                Select::make('nominee_village')->searchable()
                                    ->afterStateHydrated(function ($component, $state, ?Model $record) {
                                        if ($record) {
                                            if ($record->nominee) {
                                                if ($record->nominee->location) {
                                                    $loc = $record->nominee->location;
                                                    $record && $component->state($loc->id);
                                                }
                                            }
                                        }
                                    })
                                    ->options(function (callable $get) {
                                        return Location::query()
                                            ->where('parent_id', $get('nominee_ward'))
                                            ->type('village')
                                            ->pluck('name', 'id');
                                    })
                                    ->requiredWith('nominee_division'),
                            ])
                            ->columns(3),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('#')->rowIndex()->size('sm'),
                Tables\Columns\TextColumn::make('beneficiary_name'),
                Tables\Columns\TextColumn::make('occupation'),
                Tables\Columns\TextColumn::make('beneficiary_mobile'),
                Tables\Columns\TextColumn::make('beneficiary_nid_br'),
                Tables\Columns\TextColumn::make('location.name')->label('Village'),
                Tables\Columns\TextColumn::make('association.name'),
                Tables\Columns\TextColumn::make('nominee.nominee_name'),
                Tables\Columns\TextColumn::make('nominee.location.name')->label('Nominee location'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->slideOver()->modalWidth('7xl')->modalHeading('New Beneficiary Information')
                    ->mutateFormDataUsing(function (array $data, CreateAction $action, RelationManager $livewire) {
                        $data['location_id'] = $data['village'];
                        $data['association_id'] = $livewire->ownerRecord->id;
                        return $data;
                    })
                    ->after(function (array $data, Model $record) {
                        if (isset($data['nominee_name'])) {
                            if ($record) {
                                $beneficiaryId = $record->id;
                                $nomineeData = [
                                    'nominee_name' => $data['nominee_name'],
                                    'location_id' => $data['nominee_village'],
                                    'beneficiary_id' => $beneficiaryId,
                                    'relation_with_beneficiary' => $data['relation_with_beneficiary'],
                                ];
                            }
                            $nominee = Nominee::create($nomineeData);

                            if ($nominee) {
                                Beneficiary::where('id', '=', $beneficiaryId)->update(['nominee_id' => $nominee->id]);
                                Notification::make()->title('Beneficiary saved successfully')->success()->send();
                            } else {
                                Notification::make()->title('Something went wrong')->warning()->send();
                                Beneficiary::where('id', '=', $beneficiaryId)->delete();
                            }
                        }
                    })
                    ->successNotification(null)
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (Model $record, array $data): array {
                        $data['nominee_name'] = $record->nominee ? $record->nominee->nominee_name : null;
                        $data['relation_with_beneficiary'] = $record->nominee ? $record->nominee->relation_with_beneficiary : null;
                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data, Model $record) {
                        $data['location_id'] = $data['village'];
                        if (isset($data['nominee_name'])) {
                            if ($record->nominee) {
                                $nominee = Nominee::find($record->nominee_id);
                                $nominee->nominee_name = $data['nominee_name'];
                                $nominee->location_id = $data['nominee_village'];
                                $nominee->relation_with_beneficiary = $data['relation_with_beneficiary'];
                                $nominee->save();
                            } else {
                                $nomineeData = [
                                    'nominee_name' => $data['nominee_name'],
                                    'location_id' => $data['nominee_village'],
                                    'beneficiary_id' => $record->id,
                                    'relation_with_beneficiary' => $data['relation_with_beneficiary'],
                                ];
                                $nominee = Nominee::create($nomineeData);

                                if ($nominee)
                                    Beneficiary::where('id', '=', $record->id)->update(['nominee_id' => $nominee->id]);
                                else
                                    Beneficiary::where('id', '=', $record->id)->delete();
                            }
                        }
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Model $record) {
                        if ($record->crfBeneficiary) {
                            if ($record->crfBeneficiary->group)
                                CrfGroup::where('id', $record->crfBeneficiary->group->id)->update(['is_used' => null]);
                            if ($record->nominee)
                                Nominee::where('id', $record->nominee->id)->delete();
                            CrfBeneficiary::where('id', $record->crfBeneficiary->id)->update(['is_used' => null]);
                        } else {
                            if ($record->nominee)
                                Nominee::where('id', $record->nominee->id)->delete();
                        }
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
