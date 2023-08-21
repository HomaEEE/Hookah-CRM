<?php

namespace App\Filament\Widgets\Statistic;

use Filament\Widgets\ChartWidget;

class SupplyChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
