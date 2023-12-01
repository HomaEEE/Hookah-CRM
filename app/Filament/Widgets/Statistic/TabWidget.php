<?php

namespace App\Filament\Pages\Widgets\Statistic;

use App\Filament\Widgets\Statistic\GraphProfit;
use App\Filament\Widgets\Statistic\GraphShifts;
use App\Filament\Widgets\Statistic\GraphSupply;
use Kenepa\MultiWidget\MultiWidget;

class TabWidget extends MultiWidget
{
    public array $widgets = [
        GraphExpenses::class,
        GraphSupply::class,
        GraphShifts::class,
        GraphProfit::class
    ];
}