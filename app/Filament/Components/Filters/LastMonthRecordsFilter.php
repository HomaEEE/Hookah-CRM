<?php

namespace App\Filament\Components\Filters;

use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class LastMonthRecordsFilter extends DateRangeFilter
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('Period'))->defaultLast30Days()->timezone(config('app.timezone'));
    }
}
