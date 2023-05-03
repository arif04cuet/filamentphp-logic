<?php

namespace App\Filament\Widgets;

use App\Models\Association;
use App\Models\Beneficiary;
use App\Models\Deposit;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;
use App\Traits\DependantDepositWidget;
use Illuminate\Contracts\Support\Htmlable;
use Closure;
use Illuminate\Support\Str;

class BeneficiaryWiseDeposit extends BaseWidget
{
    use DependantDepositWidget;

    protected static ?int $sort = 4;
    protected static ?string $title = null;

    public static $association_exist = true;

    public static function canView(): bool
    {
        // return static::$association_exist;

        $permission = DB::table('permissions')->where('name', 'widget_AssociationWiseDeposit')->first();
        $access = auth()->user()->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission->name);
        })->exists();
        return $access; // can access this widget if has permission to association_wise_deposit widget
    }

    protected function getTableQuery(): Builder
    {
        $association_id = $this->getAssociationId();
        $query = Deposit::where('id', 0);
        if ($association_id) {
            static::$association_exist = true;
            $query = Beneficiary::leftJoin('deposits', 'deposits.beneficiary_id', '=', 'beneficiaries.id')
                ->select('beneficiaries.id', 'beneficiaries.beneficiary_name', DB::raw('SUM(deposits.amount) as total_deposit'))
                ->groupBy('beneficiaries.id', 'beneficiaries.beneficiary_name');

            if ($association_id) {
                $query->where('beneficiaries.association_id', $association_id);
            }
        }
        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('#')->rowIndex()->size('sm'),
            TextColumn::make('beneficiary_name')->label('Beneficiary')->wrap(),
            TextColumn::make('total_deposit'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5];
    }

    protected function getTableHeading(): string | Htmlable | Closure | null
    {
        $heading = 'Beneficiary Wise Deposit';
        if ($this->getAssociationId()) {
            $assoc = Association::where('id', $this->getAssociationId())->first();
            $heading = $assoc ? 'Beneficiary Wise Deposit of ' . $assoc->name : $heading;
        }
        return $heading;
    }
}
