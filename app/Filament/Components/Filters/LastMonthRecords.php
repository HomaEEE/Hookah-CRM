<?php

namespace App\Filament\Components\Filters;

use Auth;
use Carbon\Carbon;
use App\Models\{Expense, Supplier, Bill, ExpenseCategory};
use Filament\Facades\Filament;

use Filament\Forms\{Get, Set};
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\DB;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class LastMonthRecords
{
    public static function schema(): array
    {
        return [
            DateRangeFilter::make('created_at')->translateLabel()->separator(' - ')
            ->defaultThisMonth()
                ];
            }
        }