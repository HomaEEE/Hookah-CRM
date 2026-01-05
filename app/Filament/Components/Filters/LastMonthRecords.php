<?php

namespace App\Filament\Components\Filters;

use Filament\Tables\Filters\Filter;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class LastMonthRecords extends Filter
{
    protected function setUp(): void
    {
        $this->label('Status') // Customize the filter label
            ->schema([
                DateRangeFilter::make('created_at')->translateLabel()
                    ->defaultLast30Days(),

            ]);
    }
}
