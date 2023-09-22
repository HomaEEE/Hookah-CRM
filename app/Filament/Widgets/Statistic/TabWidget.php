<?php

namespace App\Filament\Pages\Widgets\Statistic;

use Kenepa\MultiWidget\MultiWidget;

class TabWidget extends MultiWidget
{
    public array $widgets = [
        GraphExpenses::class,
    ];
}