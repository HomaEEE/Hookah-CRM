<?php

namespace Modules\Finance\Filament\Admin\Widgets\Statistic;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Finance\Models\Bill;

class Bills extends BaseWidget
{
    // use HasWidgetShield;

    protected function getHeading(): ?string
    {
        return __('Bills');
    }

    protected function getStats(): array
    {
        return collect(Bill::all())->map(function ($bill) {
            return Stat::make($bill->name, $bill->balance);
        })->toArray();
    }
}
