<?php

namespace App\Filament\Widgets\Statistic;

use App\Models\Bill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Bills extends BaseWidget
{
    protected function getStats(): array
    {
        return collect(Bill::all())->map(function ($bill) {
            return Stat::make($bill->name, $bill->balance);
        })->toArray();
    }
}
